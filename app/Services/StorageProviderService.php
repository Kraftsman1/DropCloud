<?php

namespace App\Services;

use App\Models\StorageProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
        $user = auth()->user();
    
        if (!$user) {
            return [
                'success' => false,
                'error' => 'User is not authenticated.',
            ];
        }
    
        // Get the user's current team ID
        $teamID = $user->current_team_id;
    
        if (!$teamID) {
            return [
                'success' => false,
                'error' => 'User does not have a current team assigned.',
            ];
        }
    
        try {
            $storageProvider = new StorageProvider($data);
            
            // Save the user and team IDs
            $storageProvider->user_id = $user->id;
            $storageProvider->team_id = $teamID;
    
            // Attempt to save and check for success
            if ($storageProvider->save()) {
                return [
                    'success' => true,
                    'message' => 'Storage provider saved successfully.',
                    // 'provider' => $storageProvider->toArray()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to save storage provider.',
                ];
            }
        } catch (ValidationException $e) {
            return [
                'success' => false,
                'error' => 'Validation failed: ' . $e->getMessage(),
            ];
        } catch (\RuntimeException $e) {
            return [
                'success' => false,
                'error' => 'Connection test failed: ' . $e->getMessage(),
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

            // Validate the storage provider configuration
            $storageProvider->validateConfiguration($data);

            // Test the connection (will throw exceptions on failure)
            $storageProvider->testConnection($data);

            // Return success response
            return [
                'success' => true,
                'message' => 'Connection test successful.',
                'provider' => $storageProvider,
            ];
        } catch (\RuntimeException $e) {
            // Log and return runtime error
            Log::error('Runtime error during connection test', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            // Fallback for any other unexpected errors
            Log::error('Unexpected error during connection test', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'An unexpected error occurred: ' . $e->getMessage(),
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