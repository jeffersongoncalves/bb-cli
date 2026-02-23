<?php

namespace App\DTOs;

class Environment
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $type,
        public readonly ?int $rank = null,
    ) {}

    public static function fromApi(array $data): self
    {
        return new self(
            uuid: trim($data['uuid'] ?? '', '{}'),
            name: $data['name'] ?? '',
            slug: $data['slug'] ?? '',
            type: $data['environment_type']['name'] ?? 'Unknown',
            rank: $data['rank'] ?? null,
        );
    }
}
