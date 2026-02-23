<?php

use App\Services\GitService;
use App\Services\PullRequestService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('lists open pull requests', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pull-requests.json')), true);

    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('list')
        ->with('testworkspace', 'testrepo', Mockery::type('array'))
        ->andReturn($fixture);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:list --project=testworkspace/testrepo')
        ->expectsOutputToContain('Feature branch')
        ->expectsOutputToContain('Bugfix branch')
        ->assertExitCode(0);
});

it('shows message when no pull requests found', function () {
    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('list')
        ->andReturn(['values' => []]);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:list --project=testworkspace/testrepo')
        ->expectsOutputToContain('No results found')
        ->assertExitCode(0);
});
