<?php

namespace App\Providers;

use App\Services\AuthService;
use App\Services\GitService;
use Illuminate\Support\ServiceProvider;
use JeffersonGoncalves\LaravelZero\SelfUpdate\PharUpdater;

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

        $this->app->singleton(PharUpdater::class, fn () => new PharUpdater(
            githubRepo: 'jeffersongoncalves/bb-cli',
            assetName: 'bb.phar',
            tempPrefix: 'bb_',
            currentVersion: (string) config('app.version', 'unreleased'),
        ));
    }
}
