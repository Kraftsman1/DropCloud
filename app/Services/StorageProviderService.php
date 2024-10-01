<?php

namespace App\Services;

use App\Models\StorageProvider;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StorageProviderService
{
    /**
     * Create a new storage provider.
     *
     * @param array $data
     * @return array
     */
    public function createProvider(array $data)
    {
        try {
            $storageProvider = new StorageProvider($data);
            
            // Check if save operation succeeded
            if ($storageProvider->save()) {
                
                // Test connection after saving the provider
                if ($storageProvider->testConnection()) {
                    return [
                        'success' => true,
                        'message' => 'Storage provider saved and connection tested successfully.',
                        'provider' => $storageProvider
                    ];
                } else {
                    // Connection test failed
                    return [
                        'success' => false,
                        'error' => 'Storage provider saved, but connection test failed.'
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to save storage provider.'
                ];
            }
        } catch (ValidationException $e) {
            return [
                'success' => false,
                'error' => 'Validation failed: ' . $e->getMessage()
            ];
        } catch (\RuntimeException $e) {
            return [
                'success' => false,
                'error' => 'Connection test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all storage providers.
     *
     * @return array
     */
    public function getAllProviders()
    {
        return StorageProvider::all()->toArray();
    }

    /**
     * Get a specific storage provider by ID.
     *
     * @param int $id
     * @return array
     */
    public function getProvider($id)
    {
        try {
            $provider = StorageProvider::findOrFail($id);
            return [
                'success' => true,
                'provider' => $provider->toArray()
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'error' => 'Storage provider not found.'
            ];
        }
    }

    /**
     * Update an existing storage provider.
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateProvider($id, array $data)
    {
        try {
            $provider = StorageProvider::findOrFail($id);
            $provider->fill($data);
            $provider->save();

            if ($provider->testConnection()) {
                return [
                    'success' => true,
                    'message' => 'Storage provider updated and connection tested successfully.',
                    'provider' => $provider
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Storage provider updated, but connection test failed.'
                ];
            }
        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'error' => 'Storage provider not found.'
            ];
        } catch (ValidationException $e) {
            return [
                'success' => false,
                'error' => 'Validation failed: ' . $e->getMessage()
            ];
        } catch (\RuntimeException $e) {
            return [
                'success' => false,
                'error' => 'Connection test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a storage provider.
     *
     * @param int $id
     * @return array
     */
    public function deleteProvider($id)
    {
        try {
            $provider = StorageProvider::findOrFail($id);
            $provider->delete();
            return [
                'success' => true,
                'message' => 'Storage provider deleted successfully.'
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'error' => 'Storage provider not found.'
            ];
        }
    }

    /**
     * Test the connection for a specific storage provider.
     *
     * @param int $id
     * @return array
     */
    public function testConnection($id)
    {
        try {
            $provider = StorageProvider::findOrFail($id);
            if ($provider->testConnection()) {
                return [
                    'success' => true,
                    'message' => 'Connection test successful.',
                    'provider' => $provider
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Connection test failed.'
                ];
            }
        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'error' => 'Storage provider not found.'
            ];
        } catch (\RuntimeException $e) {
            return [
                'success' => false,
                'error' => 'Connection test failed: ' . $e->getMessage()
            ];
        }
    }
}

?>