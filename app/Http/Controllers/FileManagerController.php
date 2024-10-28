<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\StorageProvider;
use App\Services\FileManagerService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    /**
     * Display a listing of the files from the storage provider.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance.
     * @param \App\Models\StorageProvider|null $provider The storage provider instance (optional).
     * @return \Illuminate\Http\JsonResponse The JSON response containing the list of files or an error message.
     *
     * This method retrieves all available storage providers for the authenticated user.
     * If no specific provider is specified, it uses the first available provider.
     * It then sets the provider in the file manager service and attempts to list the contents
     * of the specified path. If the provider is not found, it returns a 404 response.
     * If an error occurs while listing the contents, it returns a 500 response with the error message.
     */
    public function index(Request $request, StorageProvider $provider = null)
    {
        // Get all available storage providers
        $providers = auth()->user()->storageProviders;
    
        // Use the first provider if none is specified
        $activeProvider = $provider ?? $providers->first();
    
        // Check if active provider exists, if not return 404 response
        if (!$activeProvider) {
            return response()->json([
                'success' => false,
                'error' => 'Storage provider not found.',
            ], 404);
        }
    
        // Set provider and list files
        $this->fileManagerService->setProvider($activeProvider);
        $path = $request->input('path', '');
    
        try {
            $contents = $this->fileManagerService->listContents($path);
            return response()->json([
                'success' => true,
                'contents' => $contents,
            ]);
        } catch (\RuntimeException $e) {
            // Log the error message for debugging
            Log::error('Failed to list the contents: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
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
    public function download(Request $request)
    {
        $providerId = $request->input('provider_id');
        $path = $request->input('path');

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
            return response()->streamDownload(function () use ($file) {
                fpassthru($file['stream']);
                // ensure stream is closed after download
                fclose($file['stream']);
            }, basename($path), [
                'Content-Type' => $file['mime_type'],
                'Content-Length' => $file['size'],
            ]);
        } catch (\RuntimeException $e) {
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
    public function delete(Request $request)
    {
        $providerId = $request->input('provider_id');
        $path = $request->input('path');

        $provider = StorageProvider::find($providerId);

        if (!$provider) {
            return response()->json([
                'success' => false,
                'error' => 'Storage provider not found.',
            ], 404);
        }

        $this->fileManagerService->setProvider($provider);

        try {
            $this->fileManagerService->deleteFile($path);
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
    public function upload(Request $request)
    {
        $providerId = $request->input('provider_id');
        $file = $request->file('file');
        $path = $request->input('path', '');

        $provider = StorageProvider::find($providerId);

        if (!$provider) {
            return response()->json([
                'success' => false,
                'error' => 'Storage provider not found.',
            ], 404);
        }

        $this->fileManagerService->setProvider($provider);

        try {
            $this->fileManagerService->uploadFile($file, $path);
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
