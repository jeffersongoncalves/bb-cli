<?php

use App\DTOs\Branch;

it('creates branch from API data', function () {
    $data = [
        'name' => 'feature/test',
        'target' => [
            'hash' => 'abc123def456789012345678',
            'author' => ['user' => ['display_name' => 'John Doe'], 'raw' => 'John Doe <john@example.com>'],
            'date' => '2024-01-01T10:00:00+00:00',
            'message' => 'Test commit',
        ],
    ];

    $branch = Branch::fromApi($data);

    expect($branch->name)->toBe('feature/test');
    expect($branch->hash)->toBe('abc123def456');
    expect($branch->authorUser)->toBe('John Doe');
    expect($branch->message)->toBe('Test commit');
});
