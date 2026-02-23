<?php

use App\Services\EnvironmentService;
use App\Services\GitService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('creates an environment variable', function () {
    $envService = Mockery::mock(EnvironmentService::class);
    $envService->shouldReceive('createVariable')
        ->with('testworkspace', 'testrepo', 'env-uuid-staging', 'NEW_VAR', 'new-value', false)
        ->once()
        ->andReturn([]);
    $this->app->instance(EnvironmentService::class, $envService);

    $this->artisan('env:create-variable env-uuid-staging --key=NEW_VAR --value=new-value --project=testworkspace/testrepo')
        ->expectsOutputToContain("Variable 'NEW_VAR' created")
        ->assertExitCode(0);
});
