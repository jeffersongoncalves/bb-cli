<?php

use App\Services\GitService;
use App\Services\PullRequestService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('approves a pull request', function () {
    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('approve')
        ->with('testworkspace', 'testrepo', 1)
        ->once()
        ->andReturn(['approved' => true]);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:approve 1 --project=testworkspace/testrepo')
        ->expectsOutputToContain('PR #1 approved')
        ->assertExitCode(0);
});

it('approves all open pull requests when id is 0', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pull-requests.json')), true);

    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('list')
        ->andReturn($fixture);
    $prService->shouldReceive('approve')
        ->with('testworkspace', 'testrepo', 1)->once()->andReturn([]);
    $prService->shouldReceive('approve')
        ->with('testworkspace', 'testrepo', 2)->once()->andReturn([]);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:approve 0 --project=testworkspace/testrepo')
        ->expectsOutputToContain('PR #1 approved')
        ->expectsOutputToContain('PR #2 approved')
        ->assertExitCode(0);
});
