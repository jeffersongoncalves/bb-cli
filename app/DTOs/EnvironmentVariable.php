<?php

namespace App\DTOs;

class EnvironmentVariable
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $key,
        public readonly ?string $value,
        public readonly bool $secured,
    ) {}

    public static function fromApi(array $data): self
    {
        return new self(
            uuid: trim($data['uuid'] ?? '', '{}'),
            key: $data['key'] ?? '',
            value: $data['value'] ?? null,
            secured: $data['secured'] ?? false,
        );
    }
}
