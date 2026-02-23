<?php

use App\Services\GitService;
use App\Services\PullRequestService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('requests changes on a pull request', function () {
    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('requestChanges')
        ->with('testworkspace', 'testrepo', 1)
        ->once()
        ->andReturn([]);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:request-changes 1 --project=testworkspace/testrepo')
        ->expectsOutputToContain('Changes requested')
        ->assertExitCode(0);
});
