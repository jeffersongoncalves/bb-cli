<?php

use App\Services\AuthService;
use Laravel\Prompts\Prompt;

it('saves credentials successfully', function () {
    Prompt::fallbackWhen(true);

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
