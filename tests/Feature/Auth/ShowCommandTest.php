<?php

use App\DTOs\Credentials;
use App\Services\AuthService;

it('shows saved credentials', function () {
    $credentials = new Credentials('testuser', 'myapppassword1234');

    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('load')->andReturn($credentials);
    $authService->shouldReceive('getConfigPath')->andReturn('/home/user/.bb-cli/config.json');
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:show')
        ->expectsOutputToContain('testuser')
        ->assertExitCode(0);
});

it('shows error when not authenticated', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('load')->andReturn(null);
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:show')
        ->assertExitCode(1);
});
