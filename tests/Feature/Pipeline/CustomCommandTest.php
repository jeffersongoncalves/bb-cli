<?php

use App\Services\GitService;
use App\Services\PipelineService;

beforeEach(function () {
    $gitService = Mockery::mock(GitService::class);
    $gitService->shouldReceive('resolveRepository')
        ->andReturn(['workspace' => 'testworkspace', 'repo_slug' => 'testrepo']);
    $this->app->instance(GitService::class, $gitService);
});

it('triggers a custom pipeline', function () {
    $fixture = json_decode(file_get_contents(base_path('tests/Fixtures/pipeline-pending.json')), true);

    $pipelineService = Mockery::mock(PipelineService::class);
    $pipelineService->shouldReceive('custom')
        ->with('testworkspace', 'testrepo', 'develop', 'deploy')
        ->andReturn($fixture);
    $this->app->instance(PipelineService::class, $pipelineService);

    $this->artisan('pipeline:custom develop deploy --project=testworkspace/testrepo')
        ->expectsOutputToContain('Custom pipeline #43 triggered')
        ->assertExitCode(0);
});
