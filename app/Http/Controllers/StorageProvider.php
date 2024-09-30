<?php

namespace App\Http\Controllers;

use App\Models\StorageProvider;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Services\StorageProviderService;

class StorageProviderController extends Controller
{
    /**
     * The storage provider service.
     *
     * @var StorageProviderService
     */
    protected $storageProviderService;

    /**
     * Create a new controller instance.
     *
     * @param StorageProviderService $storageProviderService
     */
    public function __construct(StorageProviderService $storageProviderService)
    {
        $this->storageProviderService = $storageProviderService;
    }

    /**
     * Get all storage providers.
     *
     * This method retrieves all storage providers using the storageProviderService
     * and returns them as a JSON response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $providers = $this->storageProviderService->getAllProviders();
        return response()->json($providers);
    }

    /**
     * Store a newly created storage provider in the database.
     *
     * @param \Illuminate\Http\Request $request The request object containing the data for the new storage provider.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the created storage provider.
     */
    public function store(Request $request)
    {
        $provider = $this->storageProviderService->createProvider($request->all());
        return response()->json($provider, $provider['success'] ? 201 : 422);
    }

    /**
     * Display the specified storage provider.
     *
     * @param int $id The ID of the storage provider to display.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the specified storage provider.
     */
    public function show($id)
    {
        $provider = $this->storageProviderService->getProvider($id);
        return response()->json($provider, $provider ? 200 : 404);
    }

    /**
     * Update the specified storage provider in the database.
     *
     * @param \Illuminate\Http\Request $request The request object containing the data for the storage provider.
     * @param int $id The ID of the storage provider to update.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the updated storage provider.
     */
    public function update(Request $request, $id)
    {
        $provider = $this->storageProviderService->updateProvider($id, $request->all());
        return response()->json($provider, $provider['success'] ? 200 : 422);
    }

    /**
     * Remove the specified storage provider from the database.
     *
     * @param int $id The ID of the storage provider to remove.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of the deletion.
     */
    public function destroy($id)
    {
        $result = $this->storageProviderService->deleteProvider($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    /**
     * Test the connection to the specified storage provider.
     *
     * @param int $id The ID of the storage provider to test.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of the connection test.
     */
    public function testConnection($id)
    {
        $result = $this->storageProviderService->testConnection($id);
        return response()->json($result, $result['success'] ? 200 : 500);
    }

}