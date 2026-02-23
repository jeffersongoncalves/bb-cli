<?php

namespace App\Commands\Pipeline;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\DTOs\Pipeline;
use App\Services\PipelineService;
use LaravelZero\Framework\Commands\Command;

class LatestCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pipeline:latest
        {--project= : The repository (owner/repo)}';

    protected $description = 'Get the latest pipeline';

    public function handle(PipelineService $pipelineService): int
    {
        return $this->handleBitbucketErrors(function () use ($pipelineService) {
            $repo = $this->resolveRepository();

            $data = $pipelineService->latest($repo['workspace'], $repo['repo_slug']);

            if (! $data) {
                $this->components->info('No pipelines found.');

                return self::SUCCESS;
            }

            $pipeline = Pipeline::fromApi($data);
            $stateLabel = $pipeline->result
                ? "{$pipeline->state->value} ({$pipeline->result})"
                : $pipeline->state->value;

            $this->components->twoColumnDetail('Build Number', (string) $pipeline->buildNumber);
            $this->components->twoColumnDetail('State', $this->colorize($stateLabel, $pipeline->state->color()));
            $this->components->twoColumnDetail('Target', $pipeline->target ?? '-');
            $this->components->twoColumnDetail('Created', $pipeline->createdOn ? $this->formatDate($pipeline->createdOn) : '-');
            $this->components->twoColumnDetail('Duration', $pipeline->durationInSeconds ? "{$pipeline->durationInSeconds}s" : '-');

            return self::SUCCESS;
        });
    }
}
