<?php

use App\DTOs\Pipeline;
use App\Enums\PipelineState;

it('creates pipeline from API data', function () {
    $data = json_decode(file_get_contents(__DIR__.'/../../Fixtures/pipeline.json'), true);
    $pipeline = Pipeline::fromApi($data);

    expect($pipeline->uuid)->toBe('pipeline-uuid-123');
    expect($pipeline->buildNumber)->toBe(42);
    expect($pipeline->state)->toBe(PipelineState::Completed);
    expect($pipeline->result)->toBe('SUCCESSFUL');
    expect($pipeline->triggerName)->toBe('push');
    expect($pipeline->target)->toBe('main');
    expect($pipeline->durationInSeconds)->toBe(300);
});

it('detects finished pipeline', function () {
    $data = json_decode(file_get_contents(__DIR__.'/../../Fixtures/pipeline.json'), true);
    $pipeline = Pipeline::fromApi($data);

    expect($pipeline->isFinished())->toBeTrue();
});

it('detects pending pipeline as not finished', function () {
    $data = json_decode(file_get_contents(__DIR__.'/../../Fixtures/pipeline-pending.json'), true);
    $pipeline = Pipeline::fromApi($data);

    expect($pipeline->isFinished())->toBeFalse();
});
