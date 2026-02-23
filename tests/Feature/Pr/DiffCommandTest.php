<?php

use App\Services\GitService;
use App\Services\PullRequestService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('shows pull request diff', function () {
    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('diff')
        ->with('testworkspace', 'testrepo', 1)
        ->andReturn("diff --git a/file.php b/file.php\n+new line");
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:diff 1 --project=testworkspace/testrepo')
        ->expectsOutputToContain('diff --git')
        ->assertExitCode(0);
});
