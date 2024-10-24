<?php

namespace App\Providers;

use App\Services\StorageProviderService;
use App\Services\FileManagerService;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StorageProviderService::class, function ($app) {
            return new StorageProviderService();
        });

        $this->app->singleton(FileManagerService::class, function ($app) {
            return new FileManagerService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
