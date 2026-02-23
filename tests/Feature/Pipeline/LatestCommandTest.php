<?php

use App\Services\GitService;
use App\Services\PipelineService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('shows latest pipeline', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pipeline.json')), true);

    $pipelineService = Mockery::mock(PipelineService::class);
    $pipelineService->shouldReceive('latest')
        ->with('testworkspace', 'testrepo')
        ->andReturn($fixture);
    $this->app->instance(PipelineService::class, $pipelineService);

    $this->artisan('pipeline:latest --project=testworkspace/testrepo')
        ->expectsOutputToContain('42')
        ->assertExitCode(0);
});

it('shows message when no pipelines found', function () {
    $pipelineService = Mockery::mock(PipelineService::class);
    $pipelineService->shouldReceive('latest')
        ->andReturn(null);
    $this->app->instance(PipelineService::class, $pipelineService);

    $this->artisan('pipeline:latest --project=testworkspace/testrepo')
        ->expectsOutputToContain('No pipelines found')
        ->assertExitCode(0);
});
