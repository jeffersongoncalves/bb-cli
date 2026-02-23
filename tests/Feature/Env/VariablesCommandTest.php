<?php

use App\Services\EnvironmentService;
use App\Services\GitService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('lists environment variables', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/environment-variables.json')), true);

    $envService = Mockery::mock(EnvironmentService::class);
    $envService->shouldReceive('variables')
        ->with('testworkspace', 'testrepo', 'env-uuid-staging')
        ->andReturn($fixture['values']);
    $this->app->instance(EnvironmentService::class, $envService);

    $this->artisan('env:variables env-uuid-staging --project=testworkspace/testrepo')
        ->expectsOutputToContain('APP_ENV')
        ->expectsOutputToContain('APP_SECRET')
        ->assertExitCode(0);
});
