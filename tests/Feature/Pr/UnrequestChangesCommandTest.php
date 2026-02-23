<?php

use App\Services\GitService;
use App\Services\PullRequestService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('removes change request from a pull request', function () {
    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('unrequestChanges')
        ->with('testworkspace', 'testrepo', 1)
        ->once()
        ->andReturn([]);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:unrequest-changes 1 --project=testworkspace/testrepo')
        ->expectsOutputToContain('Change request removed')
        ->assertExitCode(0);
});
