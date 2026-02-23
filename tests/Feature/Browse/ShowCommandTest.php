<?php

use App\Services\BrowseService;
use App\Services\GitService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('shows repository URL', function () {
    $browseService = Mockery::mock(BrowseService::class);
    $browseService->shouldReceive('getUrl')
        ->with('testworkspace', 'testrepo')
        ->andReturn('https://bitbucket.org/testworkspace/testrepo');
    $this->app->instance(BrowseService::class, $browseService);

    $this->artisan('browse:show --project=testworkspace/testrepo')
        ->expectsOutputToContain('https://bitbucket.org/testworkspace/testrepo')
        ->assertExitCode(0);
});
