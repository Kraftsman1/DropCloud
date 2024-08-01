<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

class StorageProvider extends Model
{
    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'name',
        'configuration',
    ];

    /**
     * Set the configuration attribute for the storage provider.
     *
     * @param mixed $value The value to be set as the configuration.
     * @return void
     */
    public  function setConfigurationAttribute($value)
    {
        $this->attributes['configuration'] = serialize($value);
    }

    /**
     * Get the configuration attribute.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function getConfigurationAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * Get the file system instance based on the configuration.
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function getFileSystem()
    {
        $config = $this->configuration;
        $driver = $config['driver'];

        $filesystem = Facade::getFacadeRoot()::make($driver, $config);

        return $filesystem;
    }

    /**
     * Get the users associated with the storage provider.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

}
