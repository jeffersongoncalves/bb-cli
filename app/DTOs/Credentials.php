<?php

namespace App\DTOs;

class Credentials
{
    public function __construct(
        public readonly string $username,
        public readonly string $apiToken,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'],
            apiToken: $data['api_token'] ?? $data['app_password'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'api_token' => $this->apiToken,
        ];
    }
}
