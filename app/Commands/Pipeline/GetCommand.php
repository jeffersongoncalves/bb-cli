<?php

namespace App\Commands\Pipeline;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\DTOs\Pipeline;
use App\Services\PipelineService;
use LaravelZero\Framework\Commands\Command;

class GetCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pipeline:get
        {id : Pipeline UUID or build number}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Get pipeline details';

    public function handle(PipelineService $pipelineService): int
    {
        return $this->handleBitbucketErrors(function () use ($pipelineService) {
            $repo = $this->resolveRepository();
            $id = $this->argument('id');

            $data = $pipelineService->get($repo['workspace'], $repo['repo_slug'], $id);
            $pipeline = Pipeline::fromApi($data);

            $stateLabel = $pipeline->result
                ? "{$pipeline->state->value} ({$pipeline->result})"
                : $pipeline->state->value;

            $this->components->twoColumnDetail('Build Number', (string) $pipeline->buildNumber);
            $this->components->twoColumnDetail('State', $this->colorize($stateLabel, $pipeline->state->color()));
            $this->components->twoColumnDetail('Target', $pipeline->target ?? '-');
            $this->components->twoColumnDetail('Trigger', $pipeline->triggerName ?? '-');
            $this->components->twoColumnDetail('Created', $pipeline->createdOn ? $this->formatDate($pipeline->createdOn) : '-');
            $this->components->twoColumnDetail('Completed', $pipeline->completedOn ? $this->formatDate($pipeline->completedOn) : '-');
            $this->components->twoColumnDetail('Duration', $pipeline->durationInSeconds ? "{$pipeline->durationInSeconds}s" : '-');

            return self::SUCCESS;
        });
    }
}
