<?php

namespace App\Http\Controllers;

use App\Models\StorageProvider;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Services\StorageProviderService;

use Inertia\Inertia;

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
     * Display a listing of storage providers.
     *
     * This method retrieves all storage providers using the storageProviderService
     * and returns a view rendered by Inertia with the list of providers.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $providers = $this->storageProviderService->getAllProviders();

        // return response()->json($providers);

        return Inertia::render('StorageProviders/Index', [
            'providers' => $providers
        ]);
    }

    /**
     * Display the form for creating a new storage provider.
     *
     * This method renders the 'StorageProviders/Create' view using Inertia.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('StorageProviders/Create');
    }

    /**
     * Store a newly created storage provider in the database.
     *
     * This method handles the request to create a new storage provider using the
     * provided data. It delegates the creation process to the storageProviderService
     * and then returns a response rendered by Inertia with a success or failure message
     * and a list of all storage providers.
     *
     * @param \Illuminate\Http\Request $request The incoming request containing the data for the new storage provider.
     * @return \Inertia\Response The response rendered by Inertia with a success or failure message and a list of all storage providers.
     */
    public function store(Request $request)
    {
        $provider = $this->storageProviderService->createProvider($request->all());

        // return response()->json($provider, $provider['success'] ? 201 : 422);

        return Inertia::render('StorageProviders/Index', [
            'message' => $provider['success'] ? 'Storage provider created successfully.' : 'Failed to create storage provider.',
            'providers' => $this->storageProviderService->getAllProviders()
        ]);
    }


    /**
     * Display the specified storage provider.
     *
     * @param  int  $id  The ID of the storage provider to display.
     * @return \Inertia\Response  The response containing the storage provider data.
     */
    public function show($id)
    {
        $provider = $this->storageProviderService->getProvider($id);

        // return response()->json($provider, $provider ? 200 : 404);

        return Inertia::render('StorageProviders/Show', [
            'provider' => $provider
        ]);
    }


    /**
     * Edit the specified storage provider.
     *
     * This method retrieves the storage provider by its ID and renders the
     * 'StorageProviders/Edit' view with the provider's data.
     *
     * @param int $id The ID of the storage provider to edit.
     * @return \Inertia\Response The response containing the rendered view.
     */
    public function edit($id)
    {
        $provider = $this->storageProviderService->getProvider($id);
        return Inertia::render('StorageProviders/Edit', [
            'provider' => $provider
        ]);
    }

    /**
     * Update the specified storage provider.
     *
     * @param \Illuminate\Http\Request $request The request instance containing the update data.
     * @param int $id The ID of the storage provider to update.
     * @return \Inertia\Response The response instance rendering the 'StorageProviders/Show' view.
     */
    public function update(Request $request, $id)
    {
        $provider = $this->storageProviderService->updateProvider($id, $request->all());

        // return response()->json($provider, $provider['success'] ? 200 : 422);

        return Inertia::render('StorageProviders/Show', [
            'message' => $provider['success'] ? 'Storage provider updated successfully.' : 'Failed to update storage provider.',
            'provider' => $this->storageProviderService->getProvider($id)
        ]);
    }


    /**
     * Deletes a storage provider by its ID and returns the updated list of providers.
     *
     * @param int $id The ID of the storage provider to delete.
     * @return \Inertia\Response The response containing a success or failure message and the updated list of storage providers.
     */
    public function destroy($id)
    {
        $result = $this->storageProviderService->deleteProvider($id);

        // return response()->json($result, $result['success'] ? 200 : 404);

        return Inertia::render('StorageProviders/Index', [
            'message' => $result['success'] ? 'Storage provider deleted successfully.' : 'Failed to delete storage provider.',
            'providers' => $this->storageProviderService->getAllProviders()
        ]);
    }


    /**
     * Test the connection to the storage provider.
     *
     * This method tests the connection to the storage provider with the given ID.
     * It returns a view with a message indicating whether the connection test was
     * successful or not, and the details of the storage provider.
     *
     * @param int $id The ID of the storage provider to test.
     * @return \Inertia\Response The response containing the result message and provider details.
     */
    public function testConnection(Request $request)
    {
        $data = $request->all();
        $result = $this->storageProviderService->testConnection($data);

        return response()->json( [
            'message' => $result['success'] 
            ? 'Connection test successful.'
            : ($result['error'] ?? 'Unknown Error'),
            'provider' => $result['success'] ? $result['provider'] : null
        ]);
    }

}