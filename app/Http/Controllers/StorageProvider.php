<?php

namespace App\Http\Controllers;

use App\Models\StorageProvider;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StorageProviderController extends Controller
{
    /**
     * Display a listing of the storage providers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $providers = StorageProvider::all();
        return response()->json($providers);
    }

    /**
     * Store a newly created storage provider in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'label' => 'required|string|max:255',
                'name' => 'required|string|max:255|unique:storage_providers',
                'configuration' => 'required|array',
                'configuration.driver' => 'required|string',
                // Add more validation rules for configuration based on driver
            ]);

            $storageProvider = new StorageProvider($validatedData);
            $storageProvider->save();
            
            if ($storageProvider->testConnection()) {
                return response()->json([
                    'message' => 'Storage provider created and connection tested successfully.',
                    'provider' => $storageProvider
                ], 201);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => 'Connection test failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified storage provider.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $provider = StorageProvider::findOrFail($id);
        return response()->json($provider);
    }

    /**
     * Update the specified storage provider in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $provider = StorageProvider::findOrFail($id);

            $validatedData = $request->validate([
                'label' => 'string|max:255',
                'name' => 'string|max:255|unique:storage_providers,name,' . $id,
                'configuration' => 'array',
                'configuration.driver' => 'string',
                // Add more validation rules for configuration based on driver
            ]);

            $provider->fill($validatedData);
            $provider->save();

            if ($provider->testConnection()) {
                return response()->json([
                    'message' => 'Storage provider updated and connection tested successfully.',
                    'provider' => $provider
                ]);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => 'Connection test failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified storage provider from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $provider = StorageProvider::findOrFail($id);
        $provider->delete();

        return response()->json([
            'message' => 'Storage provider deleted successfully.'
        ]);
    }

    /**
     * Test the connection for a specific storage provider.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function testConnection($id)
    {
        $provider = StorageProvider::findOrFail($id);

        try {
            if ($provider->testConnection()) {
                return response()->json([
                    'message' => 'Connection test successful.',
                    'provider' => $provider
                ]);
            }
        } catch (\RuntimeException $e) {
            return response()->json(['error' => 'Connection test failed: ' . $e->getMessage()], 500);
        }
    }
}