<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use League\Flysystem\Filesystem;

class StorageProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'name',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'array',
    ];

    /**
     * Set the configuration attribute for the storage provider.
     *
     * @param mixed $value The value to be set as the configuration.
     * @return void
     */
    public function setConfigurationAttribute($value)
    {
        $this->attributes['configuration'] = Crypt::encryptString(serialize($value));
    }

    /**
     * Get the configuration attribute for the storage provider.
     *
     * @param mixed $value The value to be set as the configuration.
     * @return mixed
     */
    public function getConfigurationAttribute($value)
    {
        return unserialize(Crypt::decryptString($value));
    }

    /**
     * Get the filesystem instance for the storage provider.
     *
     * @return Filesystem
     */
    public function getFileSystem()
    {
        $config = $this->configuration;
        $driver = $config['driver'];

        try {
            $filesystem = Facade::getFacadeRoot()::make($driver, $config);
            return $filesystem;
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to create filesystem: " . $e->getMessage());
        }
    }

    /**
     * Get the users that use this storage provider.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Validate the configuration for the storage provider.
     *
     * @return void
     */
    public function validateConfiguration()
    {
        $config = $this->configuration;

        if (!isset($config['driver'])) {
            throw ValidationException::withMessages(['driver' => 'Driver is required']);
        }

        switch ($config['driver']) {
            case 's3':
                $this->validateS3Config($config);
                break;
            case 'google':
                $this->validateGoogleConfig($config);
                break;
            // Add more cases for other storage types
            default:
                throw ValidationException::withMessages(['driver' => 'Unsupported driver']);
        }
    }

    /**
     * Validate the S3 configuration.
     *
     * @param array $config The configuration to validate.
     * @return void
     */
    private function validateS3Config($config)
    {
        $required = ['key', 'secret', 'region', 'bucket'];
        foreach ($required as $field) {
            if (!isset($config[$field])) {
                throw ValidationException::withMessages([$field => "$field is required for S3 configuration"]);
            }
        }
    }

    /**
     * Validate the Google Cloud configuration.
     *
     * @param array $config The configuration to validate.
     * @return void
     */
    private function validateGoogleConfig($config)
    {
        $required = ['project_id', 'key_file', 'bucket'];
        foreach ($required as $field) {
            if (!isset($config[$field])) {
                throw ValidationException::withMessages([$field => "$field is required for Google Cloud configuration"]);
            }
        }
    }

    /**
     * Test the connection to the storage provider.
     *
     * @return bool
     */
    public function testConnection()
    {
        try {
            $filesystem = $this->getFileSystem();
            $filesystem->listContents('');
            return true;
        } catch (\Exception $e) {
            throw new \RuntimeException("Connection test failed: " . $e->getMessage());
        }
    }

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($storageProvider) {
            $storageProvider->validateConfiguration();
        });
    }
}