<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'storage_provider_id',
        'name',
        'path',
        'mime_type',
        'size',
        'user_id',
        'metadata',
        'folder_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<int, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer',
    ];

    /**
     * Get the storage provider that the file belongs to.
     */
    public function storageProvider()
    {
        return $this->belongsTo(StorageProvider::class);
    }

    /**
     * Get the user that owns the file.
     *
     * This function defines an inverse one-to-many relationship
     * between the File model and the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Scope a query to only include files of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfTypes($query, $type)
    {
        return $query->where('mime_type', 'LIKE', $type . '%');
    }

    /**
     * Get the URL attribute for the file.
     *
     * This method generates a temporary URL for the file if a storage provider is set.
     * It uses the FileManagerService to get the temporary URL.
     *
     * @return string|null The temporary URL of the file or null if no storage provider is set or an error occurs.
     */
    public function getUrlAttribute()
    {
        if (!$this->storage_provider_id) {
            return null;
        }

        $fileManager = app(FileManagerService::class);
        $fileManager->setProvider($this->storageProvider);

        try {
            return $fileManager->getTemporaryUrl($this->path);
        } catch (\Exception $e) {
            return null;
        }
    }

}
