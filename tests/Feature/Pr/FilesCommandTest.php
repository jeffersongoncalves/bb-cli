<?php

use App\Services\GitService;
use App\Services\PullRequestService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('lists files changed in pull request', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pull-request-diffstat.json')), true);

    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('files')
        ->with('testworkspace', 'testrepo', 1)
        ->andReturn($fixture['values']);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:files 1 --project=testworkspace/testrepo')
        ->expectsOutputToContain('src/file.php')
        ->expectsOutputToContain('src/new-file.php')
        ->assertExitCode(0);
});
