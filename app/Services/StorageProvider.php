<?php

namespace App\Services;

use App\Models\StorageProvider;
use Illuminate\Validation\ValidationException;

// TODO:  Properly implement Service class to handle storage provider operations:

class StorageProviderService
{
    public function createProvider(array $data)
    {
        $storageProvider = new StorageProvider($data);

        try {
            $storageProvider->save();
            if ($storageProvider->testConnection()) {
                return [
                    'success' => true,
                    'message' => 'Storage provider saved and connection tested successfully.',
                    'provider' => $storageProvider
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
}

?>