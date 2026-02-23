<?php

namespace App\Providers;

use App\Services\AuthService;
use App\Services\GitService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //
    }

    public function register(): void
    {
        $this->app->singleton(AuthService::class);
        $this->app->singleton(GitService::class);
    }
}
