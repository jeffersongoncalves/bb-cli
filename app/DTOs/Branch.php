<?php

namespace App\DTOs;

class Branch
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $hash = null,
        public readonly ?string $authorUser = null,
        public readonly ?string $date = null,
        public readonly ?string $message = null,
    ) {}

    public static function fromApi(array $data): self
    {
        $target = $data['target'] ?? [];

        return new self(
            name: $data['name'],
            hash: isset($target['hash']) ? substr($target['hash'], 0, 12) : null,
            authorUser: $target['author']['user']['display_name'] ?? $target['author']['raw'] ?? null,
            date: $target['date'] ?? null,
            message: isset($target['message']) ? trim($target['message']) : null,
        );
    }
}
