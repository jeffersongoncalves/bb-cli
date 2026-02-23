<?php

use App\Services\GitService;
use App\Services\PipelineService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('triggers a pipeline', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pipeline-pending.json')), true);

    $pipelineService = Mockery::mock(PipelineService::class);
    $pipelineService->shouldReceive('run')
        ->with('testworkspace', 'testrepo', 'develop')
        ->andReturn($fixture);
    $this->app->instance(PipelineService::class, $pipelineService);

    $this->artisan('pipeline:run develop --project=testworkspace/testrepo')
        ->expectsOutputToContain('triggered')
        ->assertExitCode(0);
});
