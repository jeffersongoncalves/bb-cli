<?php

use App\DTOs\Credentials;

it('creates credentials from constructor', function () {
    $credentials = new Credentials('user', 'pass');

    expect($credentials->username)->toBe('user');
    expect($credentials->appPassword)->toBe('pass');
});

it('creates credentials from array', function () {
    $credentials = Credentials::fromArray([
        'username' => 'user',
        'app_password' => 'pass',
    ]);

    expect($credentials->username)->toBe('user');
    expect($credentials->appPassword)->toBe('pass');
});

it('converts to array', function () {
    $credentials = new Credentials('user', 'pass');
    $array = $credentials->toArray();

    expect($array)->toBe([
        'username' => 'user',
        'app_password' => 'pass',
    ]);
});
