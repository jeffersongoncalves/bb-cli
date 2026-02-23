<?php

use App\Services\GitService;
use App\Services\PipelineService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('shows pipeline details', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pipeline.json')), true);

    $pipelineService = Mockery::mock(PipelineService::class);
    $pipelineService->shouldReceive('get')
        ->with('testworkspace', 'testrepo', 'pipeline-uuid-123')
        ->andReturn($fixture);
    $this->app->instance(PipelineService::class, $pipelineService);

    $this->artisan('pipeline:get pipeline-uuid-123 --project=testworkspace/testrepo')
        ->expectsOutputToContain('42')
        ->expectsOutputToContain('COMPLETED')
        ->assertExitCode(0);
});
