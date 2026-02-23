<?php

use App\Services\GitService;
use App\Services\PullRequestService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('merges a pull request', function () {
    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('merge')
        ->with('testworkspace', 'testrepo', 1, [])
        ->once()
        ->andReturn([]);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:merge 1 --project=testworkspace/testrepo')
        ->expectsOutputToContain('PR #1 merged')
        ->assertExitCode(0);
});

it('merges with strategy and close source', function () {
    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('merge')
        ->with('testworkspace', 'testrepo', 1, Mockery::on(function ($opts) {
            return $opts['merge_strategy'] === 'squash' && $opts['close_source_branch'] === true;
        }))
        ->once()
        ->andReturn([]);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:merge 1 --strategy=squash --close-source --project=testworkspace/testrepo')
        ->expectsOutputToContain('PR #1 merged')
        ->assertExitCode(0);
});
