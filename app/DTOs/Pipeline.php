<?php

namespace App\DTOs;

use App\Enums\PipelineState;

class Pipeline
{
    public function __construct(
        public readonly string $uuid,
        public readonly int $buildNumber,
        public readonly PipelineState $state,
        public readonly ?string $result = null,
        public readonly ?string $triggerName = null,
        public readonly ?string $target = null,
        public readonly ?string $createdOn = null,
        public readonly ?string $completedOn = null,
        public readonly ?int $durationInSeconds = null,
    ) {}

    public static function fromApi(array $data): self
    {
        $stateKey = $data['state']['name'] ?? 'PENDING';
        $result = $data['state']['result']['name'] ?? null;

        return new self(
            uuid: trim($data['uuid'] ?? '', '{}'),
            buildNumber: $data['build_number'] ?? 0,
            state: PipelineState::tryFrom($stateKey) ?? PipelineState::Pending,
            result: $result,
            triggerName: $data['trigger']['name'] ?? null,
            target: $data['target']['ref_name'] ?? $data['target']['selector']['pattern'] ?? null,
            createdOn: $data['created_on'] ?? null,
            completedOn: $data['completed_on'] ?? null,
            durationInSeconds: $data['duration_in_seconds'] ?? null,
        );
    }

    public function isFinished(): bool
    {
        return $this->state === PipelineState::Completed;
    }
}
