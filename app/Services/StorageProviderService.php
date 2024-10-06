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
                return [
                    'success' => true,
                    'message' => 'Storage provider saved successfully.',
                    // 'provider' => $storageProvider->toArray()
                ];
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
     * Tests the connection to a storage provider.
     *
     * @param \Illuminate\Http\Request $request The request containing provider details.
     * 
     * @return array An array containing the success status, message, and provider instance if successful,
     *               or an error message if the connection test fails.
     * 
     * @throws \Illuminate\Validation\ValidationException If validation fails.
     * @throws \RuntimeException If the connection test fails due to a runtime exception.
     */
    public function testConnection(array $data) 
    {
        try {
            // Create a temporary storage provider instance
            $storageProvider = new StorageProvider($data);

            if ($storageProvider->testConnection()) {
                return [
                    'success' => true,
                    'message' => 'Connection test successful.',
                    'provider' => $storageProvider
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Connection test failed.'
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

            if ($provider->save()) {
                return [
                    'success' => true,
                    'message' => 'Storage provider updated successfully.',
                    // 'provider' => $storageProvider->toArray()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to update storage provider.'
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
    // public function testConnection($id)
    // {
    //     try {
    //         $provider = StorageProvider::findOrFail($id);
    //         if ($provider->testConnection()) {
    //             return [
    //                 'success' => true,
    //                 'message' => 'Connection test successful.',
    //                 'provider' => $provider
    //             ];
    //         } else {
    //             return [
    //                 'success' => false,
    //                 'error' => 'Connection test failed.'
    //             ];
    //         }
    //     } catch (ModelNotFoundException $e) {
    //         return [
    //             'success' => false,
    //             'error' => 'Storage provider not found.'
    //         ];
    //     } catch (\RuntimeException $e) {
    //         return [
    //             'success' => false,
    //             'error' => 'Connection test failed: ' . $e->getMessage()
    //         ];
    //     }
    // }
}

?>