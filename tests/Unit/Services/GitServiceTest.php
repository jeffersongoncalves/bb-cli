<?php

use App\Exceptions\RepositoryNotFoundException;
use App\Services\GitService;

beforeEach(function () {
    $this->gitService = new GitService;
});

it('parses SSH remote URL', function () {
    $result = $this->gitService->parseRemoteUrl('git@bitbucket.org:myworkspace/myrepo.git');

    expect($result)->toBe([
        'workspace' => 'myworkspace',
        'repo_slug' => 'myrepo',
    ]);
});

it('parses SSH remote URL without .git', function () {
    $result = $this->gitService->parseRemoteUrl('git@bitbucket.org:myworkspace/myrepo');

    expect($result)->toBe([
        'workspace' => 'myworkspace',
        'repo_slug' => 'myrepo',
    ]);
});

it('parses HTTPS remote URL', function () {
    $result = $this->gitService->parseRemoteUrl('https://bitbucket.org/myworkspace/myrepo.git');

    expect($result)->toBe([
        'workspace' => 'myworkspace',
        'repo_slug' => 'myrepo',
    ]);
});

it('parses HTTPS remote URL without .git', function () {
    $result = $this->gitService->parseRemoteUrl('https://bitbucket.org/myworkspace/myrepo');

    expect($result)->toBe([
        'workspace' => 'myworkspace',
        'repo_slug' => 'myrepo',
    ]);
});

it('parses HTTPS remote URL with credentials', function () {
    $result = $this->gitService->parseRemoteUrl('https://user@bitbucket.org/myworkspace/myrepo.git');

    expect($result)->toBe([
        'workspace' => 'myworkspace',
        'repo_slug' => 'myrepo',
    ]);
});

it('throws exception for non-Bitbucket URL', function () {
    $this->gitService->parseRemoteUrl('git@github.com:owner/repo.git');
})->throws(RepositoryNotFoundException::class);

it('resolves repository from project option', function () {
    $result = $this->gitService->resolveRepository('myworkspace/myrepo');

    expect($result)->toBe([
        'workspace' => 'myworkspace',
        'repo_slug' => 'myrepo',
    ]);
});

it('throws exception for invalid project format', function () {
    $this->gitService->resolveRepository('invalid-format');
})->throws(RepositoryNotFoundException::class);
