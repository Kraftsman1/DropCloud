<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Aws\S3\S3Client;

class StorageProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'name',
        'configuration',
        'user_id',
        'team_id',
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
     * Get the user that owns the storage provider.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the team that owns the storage provider.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the filesystem instance for the storage provider.
     *
     * @return Filesystem
     */
    public function getFileSystem(array $data = null)
    {

        $config = !empty($data) ? $data : $this->configuration; 

        if ($config['driver'] !== 's3') {
            throw new \RuntimeException("Unsupported driver: {$config['driver']}");
        }

        try {
            $client = new S3Client([
                'credentials' => [
                    'key'    => $config['key'],
                    'secret' => $config['secret'],
                ],
                'region' => $config['region'],
                'version' => 'latest',
            ]);

            $adapter = new AwsS3V3Adapter($client, $config['bucket'], $config['prefix'] ?? '');
            return new Filesystem($adapter);
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
    public function validateConfiguration(array $data = null)
    {
        $config = $data ?? $this->configuration;

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
    public function testConnection(array $data)
    {
        try {
            $filesystem = $this->getFileSystem($data);
            $filesystem->write('test.txt', 'Connection Test');
            $filesystem->delete('test.txt');
            return true;
        } catch (\Exception $e) {
            Log::error('Connection test failed', [
                'exception' => $e,
            ]);
            throw new \RuntimeException("Connection test failed : " . $e->getMessage());
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