<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\StorageProvider;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Services\FileManagerService;
use App\Services\StorageProviderService;


use Inertia\Inertia;

class FileManagerController extends Controller 
{

    /**
     * The file manager service.
     *
     * @var FileManagerService
     */
    protected $fileManagerService;

    /**
     * Create a new controller instance.
     *
     * @param FileManagerService $fileManagerService
     */
    public function __construct(FileManagerService $fileManagerService)
    {
        $this->fileManagerService = $fileManagerService;
    }

}