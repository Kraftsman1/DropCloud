<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\StorageProviderController;
use App\Http\Controllers\FileManagerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Index', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/storage-providers', [StorageProviderController::class, 'index'])->name('storage-providers.index');
    Route::get('/storage-providers/create', [StorageProviderController::class, 'create'])->name('storage-providers.create');
    Route::post('/storage-providers/test-connection', [StorageProviderController::class, 'testConnection'])->name('storage-providers.test-connection');
    Route::post('/storage-providers', [StorageProviderController::class, 'store'])->name('storage-providers.store');
    Route::get('/storage-providers/{id}', [StorageProviderController::class, 'show'])->name('storage-providers.show');
    Route::get('/storage-providers/{id}/edit', [StorageProviderController::class, 'edit'])->name('storage-providers.edit');
    Route::put('/storage-providers/{id}', [StorageProviderController::class, 'update'])->name('storage-providers.update');
    Route::delete('/storage-providers/{id}', [StorageProviderController::class, 'destroy'])->name('storage-providers.destroy');

    Route::get('/file-manager/{provider}', [FileManagerController::class, 'index'])->name('file-manager.index');
    Route::get('/file-manager/{provider}/{path}', [FileManagerController::class, 'index'])->name('file-manager.index');
    Route::get('/file-manager/{provider}/download/{path}', [FileManagerController::class, 'download'])->name('file-manager.download')->where('path', '.*');
    Route::delete('/file-manager/{provider}/delete/{path}', [FileManagerController::class, 'delete'])->name('file-manager.delete')->where('path', '.*');

});
