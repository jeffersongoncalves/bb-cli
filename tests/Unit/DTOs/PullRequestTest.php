<?php

use App\DTOs\PullRequest;
use App\Enums\PullRequestState;

it('creates pull request from API data', function () {
    $data = json_decode(file_get_contents(__DIR__.'/../../Fixtures/pull-request-single.json'), true);
    $pr = PullRequest::fromApi($data);

    expect($pr->id)->toBe(1);
    expect($pr->title)->toBe('Feature branch');
    expect($pr->state)->toBe(PullRequestState::Open);
    expect($pr->authorDisplayName)->toBe('John Doe');
    expect($pr->sourceBranch)->toBe('feature/test');
    expect($pr->destinationBranch)->toBe('main');
    expect($pr->description)->toBe('A test PR');
});
