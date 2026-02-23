<?php

use App\Services\GitService;
use App\Services\PullRequestService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('declines a pull request', function () {
    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('decline')
        ->with('testworkspace', 'testrepo', 1)
        ->once()
        ->andReturn([]);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:decline 1 --project=testworkspace/testrepo')
        ->expectsOutputToContain('PR #1 declined')
        ->assertExitCode(0);
});
