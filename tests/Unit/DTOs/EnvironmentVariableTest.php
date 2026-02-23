<?php

use App\DTOs\EnvironmentVariable;

it('creates environment variable from API data', function () {
    $data = [
        'uuid' => '{var-uuid-1}',
        'key' => 'APP_ENV',
        'value' => 'staging',
        'secured' => false,
    ];

    $var = EnvironmentVariable::fromApi($data);

    expect($var->uuid)->toBe('var-uuid-1');
    expect($var->key)->toBe('APP_ENV');
    expect($var->value)->toBe('staging');
    expect($var->secured)->toBeFalse();
});

it('handles secured variable with null value', function () {
    $data = [
        'uuid' => '{var-uuid-2}',
        'key' => 'APP_SECRET',
        'value' => null,
        'secured' => true,
    ];

    $var = EnvironmentVariable::fromApi($data);

    expect($var->value)->toBeNull();
    expect($var->secured)->toBeTrue();
});
