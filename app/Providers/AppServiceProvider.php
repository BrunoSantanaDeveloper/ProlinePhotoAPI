<?php

namespace App\Providers;

use App\Repositories\Contracts\PhotoRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\PhotoRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(PhotoRepositoryInterface::class, PhotoRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
