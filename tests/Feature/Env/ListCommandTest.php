<?php

use App\Services\EnvironmentService;
use App\Services\GitService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('lists environments', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/environments.json')), true);

    $envService = Mockery::mock(EnvironmentService::class);
    $envService->shouldReceive('list')
        ->with('testworkspace', 'testrepo')
        ->andReturn($fixture);
    $this->app->instance(EnvironmentService::class, $envService);

    $this->artisan('env:list --project=testworkspace/testrepo')
        ->expectsOutputToContain('Staging')
        ->expectsOutputToContain('Production')
        ->assertExitCode(0);
});
