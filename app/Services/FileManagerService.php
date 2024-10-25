<?php 

namespace App\Services;

use App\Models\StorageProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;

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
        $this->filesystem = $provider->getFilesystem($provider);
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
            $contents = $this->filesystem->listContents($path, $recursive);
            return $contents;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to list contents: ' . $e->getMessage());
        }
    }

    public function downloadFile(string $path) 
    {
        try {
            if (!$this->filesystem->fileExists($path)) {
                throw new \RuntimeException("File not found: {$path}");
            }

            return [
                'stream' => $this->filesystem->readStream($path),
                'mime_type' => $this->filesystem->mimeType($path),
                'size' => $this->filesystem->fileSize($path),
            ];
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to download file: {$e->getMessage()}");
        }
    }

    

}

?>