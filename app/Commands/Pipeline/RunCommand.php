<?php

namespace App\Commands\Pipeline;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\DTOs\Pipeline;
use App\Services\PipelineService;
use LaravelZero\Framework\Commands\Command;

class RunCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pipeline:run
        {branch : Branch to run the pipeline on}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Trigger a pipeline for a branch';

    public function handle(PipelineService $pipelineService): int
    {
        return $this->handleBitbucketErrors(function () use ($pipelineService) {
            $repo = $this->resolveRepository();
            $branch = $this->argument('branch');

            $data = $pipelineService->run($repo['workspace'], $repo['repo_slug'], $branch);
            $pipeline = Pipeline::fromApi($data);

            $this->components->info("Pipeline #{$pipeline->buildNumber} triggered on branch '{$branch}'.");
            $this->components->twoColumnDetail('UUID', $pipeline->uuid);
            $this->components->twoColumnDetail('State', $this->colorize($pipeline->state->value, $pipeline->state->color()));

            return self::SUCCESS;
        });
    }
}
