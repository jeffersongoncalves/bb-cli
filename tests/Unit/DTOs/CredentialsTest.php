<?php

use App\DTOs\Credentials;

it('creates credentials from constructor', function () {
    $credentials = new Credentials('user', 'pass');

    expect($credentials->username)->toBe('user');
    expect($credentials->apiToken)->toBe('pass');
});

it('creates credentials from array', function () {
    $credentials = Credentials::fromArray([
        'username' => 'user',
        'api_token' => 'pass',
    ]);

    expect($credentials->username)->toBe('user');
    expect($credentials->apiToken)->toBe('pass');
});

it('creates credentials from legacy app_password format', function () {
    $credentials = Credentials::fromArray([
        'username' => 'user',
        'app_password' => 'legacy-pass',
    ]);

    expect($credentials->username)->toBe('user');
    expect($credentials->apiToken)->toBe('legacy-pass');
});

it('converts to array', function () {
    $credentials = new Credentials('user', 'pass');
    $array = $credentials->toArray();

    expect($array)->toBe([
        'username' => 'user',
        'api_token' => 'pass',
    ]);
});
