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

    // Storage Provider Route Group
    Route::prefix('storage-providers')->group(function () {
        Route::get('/', [StorageProviderController::class, 'index'])->name('storage-providers.index');
        Route::get('/create', [StorageProviderController::class, 'create'])->name('storage-providers.create');
        Route::post('/test-connection', [StorageProviderController::class, 'testConnection'])->name('storage-providers.test-connection');
        Route::post('/', [StorageProviderController::class, 'store'])->name('storage-providers.store');
        Route::get('/{id}', [StorageProviderController::class, 'show'])->name('storage-providers.show');
        Route::get('/{id}/edit', [StorageProviderController::class, 'edit'])->name('storage-providers.edit');
        Route::put('/{id}', [StorageProviderController::class, 'update'])->name('storage-providers.update');
        Route::delete('/{id}', [StorageProviderController::class, 'destroy'])->name('storage-providers.destroy');
    });

    // File Manager Route Group
    Route::prefix('file-manager')->group(function () {
        Route::get('/{provider}/{path?}', [FileManagerController::class, 'index'])->name('file-manager.index');
        Route::get('/{provider}/download/{path}', [FileManagerController::class, 'download'])->name('file-manager.download')->where('path', '.*');
        Route::delete('/{provider}/delete/{path}', [FileManagerController::class, 'delete'])->name('file-manager.delete')->where('path', '.*');
        Route::post('/{provider}/upload', [FileManagerController::class, 'upload'])->name('file-manager.upload');
    });

});
