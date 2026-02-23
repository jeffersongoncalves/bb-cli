<?php

namespace App\Commands\Pipeline;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\DTOs\Pipeline;
use App\Services\PipelineService;
use LaravelZero\Framework\Commands\Command;

class WaitCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pipeline:wait
        {id? : Pipeline UUID (defaults to latest)}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Wait for a pipeline to complete';

    public function handle(PipelineService $pipelineService): int
    {
        return $this->handleBitbucketErrors(function () use ($pipelineService) {
            $repo = $this->resolveRepository();
            $id = $this->argument('id');

            if (! $id) {
                $latest = $pipelineService->latest($repo['workspace'], $repo['repo_slug']);
                if (! $latest) {
                    $this->components->error('No pipelines found.');

                    return self::FAILURE;
                }
                $id = trim($latest['uuid'] ?? '', '{}');
            }

            $this->components->info("Waiting for pipeline {$id}...");

            $pipeline = $pipelineService->wait(
                $repo['workspace'],
                $repo['repo_slug'],
                $id,
                function (Pipeline $p) {
                    $this->output->write("\r  " . $this->colorize($p->state->value, $p->state->color()) . ' ');
                },
            );

            $this->newLine();

            $result = $pipeline->result ?? 'UNKNOWN';
            $color = strtoupper($result) === 'SUCCESSFUL' ? 'green' : 'red';

            $this->components->twoColumnDetail('Result', $this->colorize($result, $color));
            $this->components->twoColumnDetail('Duration', $pipeline->durationInSeconds ? "{$pipeline->durationInSeconds}s" : '-');

            return strtoupper($result) === 'SUCCESSFUL' ? self::SUCCESS : self::FAILURE;
        });
    }
}
