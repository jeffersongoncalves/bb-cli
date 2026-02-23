<?php

use App\Services\AuthService;

it('saves credentials successfully', function () {
    $authService = Mockery::mock(AuthService::class);
    $authService->shouldReceive('save')->once()->with('testuser', 'testpass');
    $authService->shouldReceive('getConfigPath')->andReturn('/home/user/.bb-cli/config.json');
    $this->app->instance(AuthService::class, $authService);

    $this->artisan('auth:save')
        ->expectsQuestion('Bitbucket username', 'testuser')
        ->expectsQuestion('Bitbucket app password', 'testpass')
        ->expectsOutputToContain('Credentials saved')
        ->assertExitCode(0);
});
