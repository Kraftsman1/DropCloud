<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\StorageProvider;
use App\Services\FileManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class FileManagerController extends Controller
{

    /**
     * The file manager service.
     *
     * @var FileManagerService
     */
    protected $fileManagerService;

    /**
     * Create a new controller instance.
     *
     * @param FileManagerService $fileManagerService
     */
    public function __construct(FileManagerService $fileManagerService)
    {
        $this->fileManagerService = $fileManagerService;
    }

    public function index(Request $request, StorageProvider $provider = null)
    {
        // Get the provider id from the route if not provided in parameter
        $providerId = $provider ? $provider->id : $request->route('provider');

        // Get the authenticated user
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User is not authenticated.',
            ], 401);
        }
        
        // Get the user's current team ID
        $teamID = $user->current_team_id;
        
        if (!$teamID) {
            return response()->json([
                'success' => false,
                'error' => 'User does not have a current team assigned.',
            ], 403);
        }
        
        // Get all available storage providers for the current team
        $providers = StorageProvider::where('team_id', $teamID)->get();
    
        // Use the specified provider or the first one if none is provided
        $activeProvider = $provider ?? $providers->firstWhere('id', $providerId);

        // Check if the active provider exists, if not return 404 response
        if (!$activeProvider) {
            return response()->json([
                'success' => false,
                'error' => 'Storage provider not found.',
            ], 404);
        }
        
        // Set provider and list files
        $this->fileManagerService->setProvider($activeProvider);
        $path = $request->route('path', '');

        try {
            // Assuming `listContents` requires a `$recursive` argument (use true or false based on requirements)
            $contents = $this->fileManagerService->listContents($path, false);
            return Inertia::render('FileManager/Index', [
                'contents' => $contents,
                'provider' => $activeProvider,
                'providers' => $providers,
            ]);
        } catch (\RuntimeException $e) {
            // Log the error message for debugging
            Log::error('Failed to list the contents: ' . $e->getMessage(), ['exception' => $e]);
            return Inertia::render('StorageProvider/Index', [
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    /**
     * Download a file from the storage provider.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * This method retrieves the provider ID and path from the request, finds the corresponding
     * storage provider, and downloads the file specified by the path. If the provider is not found,
     * it returns a 404 error response. If an exception occurs during the download, it returns a 500
     * error response with the exception message.
     *
     * @throws \RuntimeException If an error occurs while downloading the file.
     */
    public function download(Request $request, StorageProvider $provider = null, $path = null)
    {

        $providerId = $provider ? $provider->id : $request->route('provider');
        $path = urldecode($request->route('path'));

        $provider = StorageProvider::find($providerId);

        if (!$provider) {
            return response()->json([
                'success' => false,
                'error' => 'Storage provider not found.',
            ], 404);
        }

        $this->fileManagerService->setProvider($provider);

        try {
            $file = $this->fileManagerService->downloadFile($path);

            // Validate the file stream before streaming
            if(!is_resource($file['stream'])) {
                throw new \RuntimeException('Invalid file stream.');
            }

            return response()->streamDownload(function () use ($file) {
                // Attempt to output stream with error handling
                if (fpassthru($file['stream']) === false) {
                    throw new \RuntimeException('Failed to output file stream.');
                }

                // ensure stream is closed after download
                fclose($file['stream']);
            }, basename($path), [
                'Content-Type' => $file['mime_type'],
                'Content-Length' => $file['size'],
            ]);
        } catch (\RuntimeException $e) {
            Log::error('File download failed', [
                'error' => $e->getMessage(), 
                'path' => $path
            ]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a file from the storage provider.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * This method retrieves the provider ID and path from the request, finds the corresponding
     * storage provider, and deletes the file specified by the path. If the provider is not found,
     * it returns a 404 error response. If an exception occurs during the deletion, it returns a 500
     * error response with the exception message.
     *
     * @throws \RuntimeException If an error occurs while deleting the file.
     */
    public function delete(Request $request, StorageProvider $provider = null, $path = null)
    {
        $providerId = $provider ? $provider->id : $request->route('provider');
        $path = $request->route('path');

        $provider = StorageProvider::find($providerId);

        if (!$provider) {
            return response()->json([
                'success' => false,
                'error' => 'Storage provider not found.',
            ], 404);
        }

        $this->fileManagerService->setProvider($provider);

        try {
            $this->fileManagerService->delete($path);
            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // TODO: Implement and Fix File/Folder Upload

    /**
     * Upload a file to the storage provider.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * This method retrieves the provider ID and file from the request, finds the corresponding
     * storage provider, and uploads the file to the specified path. If the provider is not found,
     * it returns a 404 error response. If an exception occurs during the upload, it returns a 500
     * error response with the exception message.
     *
     * @throws \RuntimeException If an error occurs while uploading the file.
     */
    public function upload(Request $request, StorageProvider $provider = null, $path = null)
    {
        $providerId = $provider ? $provider->id : $request->route('provider');
        $path = $request->route('path', '');
    
        $provider = StorageProvider::find($providerId);
    
        if (!$provider) {
            return response()->json([
                'success' => false,
                'error' => 'Storage provider not found.',
            ], 404);
        }
    
        $this->fileManagerService->setProvider($provider);
    
        try {
            // Handle both single and multiple file uploads
            $files = $request->validate([
                'files.*' => 'max:100000',
            ]);
            $files = $request->file('files');
            
            // Ensure $files is always an array
            $files = is_array($files) ? $files : [$files];
    
            $uploadedFiles = [];
            foreach ($files as $file) {
                $uploadedFile= $this->fileManagerService->uploadFile($file, $path);

                $uploadedFiles[] = $uploadedFile;
            }
    
            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' file(s) uploaded successfully.',
                'uploads' => $uploadedFiles
            ]);
        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(), 
                'path' => $path
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'File upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    // TODO: Implement and Fix File/Folder Creation

}
