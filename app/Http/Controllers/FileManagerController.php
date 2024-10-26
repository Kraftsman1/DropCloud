<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\StorageProvider;
use App\Services\FileManagerService;
use Illuminate\Http\Request;

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
     * Display a listing of the files and directories.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * This method retrieves the provider ID and path from the request, finds the corresponding
     * storage provider, and lists the contents of the specified path. If the provider is not found,
     * it returns a 404 error response. If an exception occurs during the listing of contents, it
     * returns a 500 error response with the exception message.
     *
     * @throws \RuntimeException If an error occurs while listing the contents.
     */
    public function index(Request $request)
    {
        $providerId = $request->input('provider_id');
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
            $contents = $this->fileManagerService->listContents($path);
            return response()->json([
                'success' => true,
                'contents' => $contents,
            ]);
        } catch (\RuntimeException $e) {
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

}
