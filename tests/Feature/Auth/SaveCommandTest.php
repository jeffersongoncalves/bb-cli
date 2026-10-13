<?php

use App\Services\AuthService;
use Laravel\Prompts\Prompt;

it('saves credentials successfully', function () {
    Prompt::fallbackWhen(true);

    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('save')->once();
    $authService->shouldReceive('getConfigPath')->andReturn('/home/user/.bb-cli/config.json');
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:save --interactive')
        ->expectsQuestion('Bitbucket account email', 'testuser')
        ->expectsQuestion('Bitbucket API token', 'testpass')
        ->expectsOutputToContain('Credentials saved')
        ->assertExitCode(0);
});

it('saves credentials from options without prompting', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('save')->once();
    $authService->shouldReceive('getConfigPath')->andReturn('/home/user/.bb-cli/config.json');
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:save --email=testuser --token=testpass')
        ->expectsOutputToContain('Credentials saved')
        ->assertExitCode(0);
});

it('fails instead of prompting when credentials are missing', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldNotReceive('save');
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:save')
        ->expectsOutputToContain('Missing --email')
        ->assertExitCode(1);
});
