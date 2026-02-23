<?php

use App\Services\EnvironmentService;
use App\Services\GitService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('updates an environment variable', function () {
    $envService = Mockery::mock(EnvironmentService::class);
    $envService->shouldReceive('updateVariable')
        ->with('testworkspace', 'testrepo', 'env-uuid-staging', 'var-uuid-1', 'updated-value', false)
        ->once()
        ->andReturn([]);
    $this->app->instance(EnvironmentService::class, $envService);

    $this->artisan('env:update-variable env-uuid-staging --variable=var-uuid-1 --value=updated-value --project=testworkspace/testrepo')
        ->expectsOutputToContain('Variable updated')
        ->assertExitCode(0);
});
