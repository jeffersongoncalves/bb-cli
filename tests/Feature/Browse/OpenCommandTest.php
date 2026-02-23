<?php

use App\Services\BrowseService;
use App\Services\GitService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('opens repository in browser', function () {
    $browseService = Mockery::mock(BrowseService::class);
    $browseService->shouldReceive('getUrl')
        ->with('testworkspace', 'testrepo')
        ->andReturn('https://bitbucket.org/testworkspace/testrepo');
    $browseService->shouldReceive('open')
        ->with('https://bitbucket.org/testworkspace/testrepo')
        ->once();
    $this->app->instance(BrowseService::class, $browseService);

    $this->artisan('browse --project=testworkspace/testrepo')
        ->expectsOutputToContain('Opening')
        ->assertExitCode(0);
});
