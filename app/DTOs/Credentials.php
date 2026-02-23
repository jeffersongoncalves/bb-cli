<?php

namespace App\DTOs;

class Credentials
{
    public function __construct(
        public readonly string $username,
        public readonly string $appPassword,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'],
            appPassword: $data['app_password'],
        );
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'app_password' => $this->appPassword,
        ];
    }
}
