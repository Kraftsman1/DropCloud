<?php

namespace App\Services;

use App\Models\StorageProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;

class FileManagerService
{

    /**
     * @var mixed $provider The provider instance for the file manager service.
     * @var \Illuminate\Filesystem\Filesystem $filesystem The filesystem instance used for file operations.
     */
    protected $provider;
    protected $filesystem;

    /**
     * Constructor for the FileManagerService class.
     *
     * @param StorageProvider|null $provider Optional storage provider to set.
     */
    public function __contruct(StorageProvider $provider = null)
    {
        if ($provider) {
            $this->setProvider($provider);
        }
    }

    /**
     * Sets the storage provider and initializes the filesystem.
     *
     * @param StorageProvider $provider The storage provider instance.
     */
    public function setProvider(StorageProvider $provider)
    {
        $this->provider = $provider;
        $this->filesystem = $provider->getFilesystem($provider->configuration);
    }

    /**
     * List the contents of a directory.
     *
     * @param string $path The path of the directory to list. Defaults to an empty string.
     * @param bool $recursive Whether to list contents recursively. Defaults to false.
     * @return array The contents of the directory.
     * @throws \RuntimeException If listing the contents fails.
     */
    public function listContents(string $path = '', bool $recursive = false)
    {
        try {
            if (!$this->filesystem) {
                $this->filesystem = $this->getFileSystem();
            }

            $contents = $this->filesystem->listContents($path, $recursive);

            $files = [];
            $folders = [];

            foreach ($contents as $content) {
                $metadata = [
                    'path' => $content->path(),
                    'type' => $content->type(),
                ];

                if ($content instanceof \League\Flysystem\FileAttributes) {
                    $metadata['size'] = $this->filesystem->fileSize($content->path());
                    $metadata['mime_type'] = $this->filesystem->mimeType($content->path());
                    $metadata['last_modified'] = $this->filesystem->lastModified($content->path());
                    $metadata['visibility'] = $this->filesystem->visibility($content->path());
                    $files[] = $metadata;
                } else {
                    $folders[] = $metadata;
                }
            }

            return [
                'success' => true,
                'data' => [
                    'files' => $files,
                    'folders' => $folders,
                    'path' => $path,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to list contents: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Uploads a file to the specified path with optional metadata and visibility settings.
     *
     * @param \Illuminate\Http\UploadedFile $file The file to be uploaded.
     * @param string $path The destination path where the file should be uploaded.
     * @param array $options Optional settings for the upload:
     *                       - 'filename' (string): Custom filename for the uploaded file.
     *                       - 'visibility' (string): Visibility setting for the file (default: 'private').
     * @return array An array containing the success status, file path, and metadata.
     * @throws \RuntimeException If the file upload fails.
     */
    public function uploadFile(UploadedFile $file, string $path, array $options = [])
    {
        try {
            if (!$file->isValid()) {
                throw new \RuntimeException("Invalid file upload: {$file->getClientOriginalName()}");
            }
    
            $path = trim($path, '/');
            $filename = $options['filename'] ?? $file->getClientOriginalName();
            $fullPath = ($path ? $path . '/' : '') . $filename;
    
            $metadata = [
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
                'uploaded_at' => now(),
            ];
    
            // Use file stream directly
            $stream = fopen($file->getRealPath(), 'r');
    
            if (!$stream) {
                throw new \RuntimeException("Failed to open file stream for {$filename}");
            }
    
            $this->filesystem->writeStream($fullPath, $stream, [
                'metadata' => $metadata,
                'visibility' => $options['visibility'] ?? 'private',
            ]);
    
            // Close the stream after upload
            fclose($stream);
    
            return [
                'success' => true,
                'path' => $fullPath,
                'metadata' => $metadata,
            ];
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to upload file: {$e->getMessage()}");
        }
    }

    /**
     * Download a file from the filesystem.
     *
     * @param string $path The path to the file to be downloaded.
     *
     * @return array An associative array containing:
     *               - 'stream': The file stream.
     *               - 'mime_type': The MIME type of the file.
     *               - 'size': The size of the file in bytes.
     *
     * @throws \RuntimeException If the file does not exist or if there is an error during the download process.
     */
    public function downloadFile(string $path)
    {
        try {
            // Log additional information for debugging
            Log::info('Attempting to download file', [
                'path' => $path,
                'filesystem' => get_class($this->filesystem),
                'file_exists' => $this->filesystem->fileExists($path),
            ]);

            if (!$this->filesystem->fileExists($path)) {
                Log::error('File not found', ['path' => $path]);
                throw new \RuntimeException("File not found: {$path}");
            }

            $stream = $this->filesystem->readStream($path);

            if ($stream === false) {
                Log::error('Failed to read file stream', ['path' => $path]);
                throw new \RuntimeException("Failed to read file stream: {$path}");
            }

            return [
                'stream' => $stream,
                'mime_type' => $this->filesystem->mimeType($path),
                'size' => $this->filesystem->fileSize($path),
            ];
        } catch (\Exception $e) {
            Log::error('Download file error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'path' => $path,
            ]);
            throw new \RuntimeException("Failed to download file: {$e->getMessage()}");
        }
    }

    /**
     * Deletes a file or directory at the specified path.
     *
     * This method attempts to delete a file if it exists at the given path.
     * If the path points to a directory, it will attempt to delete the directory.
     * If neither a file nor a directory exists at the path, an exception is thrown.
     *
     * @param string $path The path to the file or directory to be deleted.
     * @return array An array containing a success key with a boolean value.
     * @throws \RuntimeException If the path does not exist or if the deletion fails.
     */
    public function delete(string $path)
    {
        try {
            if ($this->filesystem->fileExists($path)) {
                $this->filesystem->delete($path);
            } elseif ($this->filesystem->directoryExists($path)) {
                $this->filesystem->deleteDirectory($path);
            } else {
                throw new \RuntimeException("Path not found: {$path}");
            }

            return ['success' => true];
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to delete: {$e->getMessage()}");
        }
    }

    /**
     * Retrieves metadata for a given file path.
     *
     * This method attempts to gather various metadata attributes for the specified file path,
     * including MIME type, file size, last modified timestamp, and visibility status.
     *
     * @param string $path The path to the file for which metadata is being retrieved.
     *
     * @return array An associative array containing the following keys:
     *               - 'mime_type': The MIME type of the file.
     *               - 'size': The size of the file in bytes.
     *               - 'last_modified': The last modified timestamp of the file.
     *               - 'visibility': The visibility status of the file.
     *
     * @throws \RuntimeException If an error occurs while retrieving the metadata.
     */
    public function getMetadata(string $path)
    {
        try {
            return [
                'mime_type' => $this->filesystem->mimeType($path),
                'size' => $this->filesystem->fileSize($path),
                'last_modified' => $this->filesystem->lastModified($path),
                'visibility' => $this->filesystem->visibility($path),
            ];
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to get metadata: {$e->getMessage()}");
        }
    }

}
?>
