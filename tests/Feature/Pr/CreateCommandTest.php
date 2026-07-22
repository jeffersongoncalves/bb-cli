<?php

use App\Services\GitService;
use App\Services\PullRequestService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('creates a pull request with options', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pull-request-created.json')), true);

    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('findOpenBySourceBranch')
        ->with('testworkspace', 'testrepo', 'feature/new')
        ->once()
        ->andReturn(null);
    $prService->shouldReceive('create')
        ->with('testworkspace', 'testrepo', Mockery::type('array'))
        ->once()
        ->andReturn($fixture);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:create feature/new main --title="New feature" --description="Description" --project=testworkspace/testrepo')
        ->expectsOutputToContain('PR #3 created')
        ->assertExitCode(0);
});

it('updates the open pull request for the source branch instead of creating a new one', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pull-request-created.json')), true);

    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('findOpenBySourceBranch')
        ->with('testworkspace', 'testrepo', 'feature/new')
        ->once()
        ->andReturn(['id' => 3]);
    $prService->shouldReceive('update')
        ->with('testworkspace', 'testrepo', 3, Mockery::type('array'))
        ->once()
        ->andReturn($fixture);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:create feature/new main --title="New feature" --description="Description" --project=testworkspace/testrepo')
        ->expectsOutputToContain('PR #3 updated')
        ->assertExitCode(0);
});
