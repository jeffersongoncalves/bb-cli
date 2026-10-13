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

it('sends the summary without markup', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pull-request-created.json')), true);

    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('findOpenBySourceBranch')->andReturn(null);
    $prService->shouldReceive('create')
        ->with('testworkspace', 'testrepo', Mockery::on(fn ($data) => $data['summary'] === ['raw' => 'Description']))
        ->once()
        ->andReturn($fixture);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:create feature/new main --title="New feature" --description="Description" --project=testworkspace/testrepo')
        ->assertExitCode(0);
});

it('never prompts, defaulting the title to the source branch and the description to empty', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pull-request-created.json')), true);

    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('findOpenBySourceBranch')->andReturn(null);
    $prService->shouldReceive('create')
        ->with('testworkspace', 'testrepo', Mockery::on(fn ($data) => $data['title'] === 'feature/new' && $data['summary'] === ['raw' => '']))
        ->once()
        ->andReturn($fixture);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:create feature/new main --project=testworkspace/testrepo')
        ->assertExitCode(0);
});

it('reads a multiline description from --description-file', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pull-request-created.json')), true);
    $file = tempnam(sys_get_temp_dir(), 'bb');
    file_put_contents($file, "line 1\nline 2");

    $prService = Mockery::mock(PullRequestService::class);
    $prService->shouldReceive('findOpenBySourceBranch')->andReturn(null);
    $prService->shouldReceive('create')
        ->with('testworkspace', 'testrepo', Mockery::on(fn ($data) => $data['summary'] === ['raw' => "line 1\nline 2"]))
        ->once()
        ->andReturn($fixture);
    $this->app->instance(PullRequestService::class, $prService);

    $this->artisan('pr:create', [
        'source' => 'feature/new',
        'destination' => 'main',
        '--description-file' => $file,
        '--project' => 'testworkspace/testrepo',
    ])->assertExitCode(0);

    unlink($file);
});

it('fails when the description file cannot be read', function () {
    $this->app->instance(PullRequestService::class, Mockery::mock(PullRequestService::class));

    $this->artisan('pr:create feature/new main --description-file=missing.md --project=testworkspace/testrepo')
        ->expectsOutputToContain('Unable to read description file')
        ->assertExitCode(1);
});
