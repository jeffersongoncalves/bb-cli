<?php

namespace App\DTOs;

use JeffersonGoncalves\LaravelZero\Credentials\CredentialsContract;

final class Credentials implements CredentialsContract
{
    public function __construct(
        public readonly string $username,
        public readonly string $apiToken,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            username: $data['username'] ?? '',
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

    public function isValid(): bool
    {
        return $this->username !== '' && $this->apiToken !== '';
    }
}
