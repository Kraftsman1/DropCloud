<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageProvider extends Model
{
    use HasFactory;

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
     * Get the users associated with the storage provider.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

}
