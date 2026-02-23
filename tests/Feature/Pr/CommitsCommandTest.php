<?php

use App\Services\GitService;
use App\Services\PullRequestService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('lists commits in pull request', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pull-request-commits.json')), true);

    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('commits')
        ->with('testworkspace', 'testrepo', 1)
        ->andReturn($fixture['values']);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:commits 1 --project=testworkspace/testrepo')
        ->expectsOutputToContain('Initial commit')
        ->expectsOutputToContain('Add tests')
        ->assertExitCode(0);
});
