<?php

use App\DTOs\Environment;

it('creates environment from API data', function () {
    $data = [
        'uuid' => '{env-uuid-staging}',
        'name' => 'Staging',
        'slug' => 'staging',
        'environment_type' => ['name' => 'Staging'],
        'rank' => 1,
    ];

    $env = Environment::fromApi($data);

    expect($env->uuid)->toBe('env-uuid-staging');
    expect($env->name)->toBe('Staging');
    expect($env->slug)->toBe('staging');
    expect($env->type)->toBe('Staging');
    expect($env->rank)->toBe(1);
});
