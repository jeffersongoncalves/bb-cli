<?php

use App\Services\BranchService;
use App\Services\GitService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('lists branches', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/branches.json')), true);

    $branchService = Mockery::mock(BranchService::class);
    $branchService->shouldReceive('list')
        ->with('testworkspace', 'testrepo')
        ->andReturn($fixture);
    $this->app->instance(BranchService::class, $branchService);

    $this->artisan('branch:list --project=testworkspace/testrepo')
        ->expectsOutputToContain('main')
        ->expectsOutputToContain('feature/test')
        ->assertExitCode(0);
});
