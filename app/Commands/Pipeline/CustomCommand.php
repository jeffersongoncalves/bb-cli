<?php

namespace App\Commands\Pipeline;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\DTOs\Pipeline;
use App\Services\PipelineService;
use LaravelZero\Framework\Commands\Command;

class CustomCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pipeline:custom
        {branch : Branch to run the pipeline on}
        {pattern : Custom pipeline pattern name}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Trigger a custom pipeline';

    public function handle(PipelineService $pipelineService): int
    {
        return $this->handleBitbucketErrors(function () use ($pipelineService) {
            $repo = $this->resolveRepository();
            $branch = $this->argument('branch');
            $pattern = $this->argument('pattern');

            $data = $pipelineService->custom($repo['workspace'], $repo['repo_slug'], $branch, $pattern);
            $pipeline = Pipeline::fromApi($data);

            $this->components->info("Custom pipeline #{$pipeline->buildNumber} triggered (pattern: '{$pattern}') on branch '{$branch}'.");
            $this->components->twoColumnDetail('UUID', $pipeline->uuid);
            $this->components->twoColumnDetail('State', $this->colorize($pipeline->state->value, $pipeline->state->color()));

            return self::SUCCESS;
        });
    }
}
