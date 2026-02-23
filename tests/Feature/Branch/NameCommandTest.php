<?php

use App\Services\BranchService;
use App\Services\GitService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('lists branches by name pattern', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/branches.json')), true);

    $branchService = Mockery::mock(BranchService::class);
    $branchService->shouldReceive('filterByName')
        ->with('testworkspace', 'testrepo', 'feature')
        ->andReturn([$fixture['values'][1]]);
    $this->app->instance(BranchService::class, $branchService);

    $this->artisan('branch:name feature --project=testworkspace/testrepo')
        ->expectsOutputToContain('feature/test')
        ->assertExitCode(0);
});
