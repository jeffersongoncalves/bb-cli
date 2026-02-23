<?php

use App\Services\GitService;
use App\Services\PullRequestService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('removes approval from a pull request', function () {
    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('unapprove')
        ->with('testworkspace', 'testrepo', 1)
        ->once()
        ->andReturn([]);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:unapprove 1 --project=testworkspace/testrepo')
        ->expectsOutputToContain('approval removed')
        ->assertExitCode(0);
});
