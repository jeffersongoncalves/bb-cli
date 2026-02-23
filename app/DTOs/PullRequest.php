<?php

namespace App\DTOs;

use App\Enums\PullRequestState;

class PullRequest
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly PullRequestState $state,
        public readonly string $authorDisplayName,
        public readonly string $sourceBranch,
        public readonly string $destinationBranch,
        public readonly string $createdOn,
        public readonly string $updatedOn,
        public readonly ?string $description = null,
    ) {}

    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'],
            state: PullRequestState::from($data['state']),
            authorDisplayName: $data['author']['display_name'] ?? 'Unknown',
            sourceBranch: $data['source']['branch']['name'] ?? '',
            destinationBranch: $data['destination']['branch']['name'] ?? '',
            createdOn: $data['created_on'] ?? '',
            updatedOn: $data['updated_on'] ?? '',
            description: $data['description'] ?? null,
        );
    }
}
