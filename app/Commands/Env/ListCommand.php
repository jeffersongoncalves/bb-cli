<?php

namespace App\Commands\Env;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\DTOs\Environment;
use App\Services\EnvironmentService;
use LaravelZero\Framework\Commands\Command;

class ListCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'env:list
        {--project= : The repository (owner/repo)}';

    protected $description = 'List deployment environments';

    public function handle(EnvironmentService $envService): int
    {
        return $this->handleBitbucketErrors(function () use ($envService) {
            $repo = $this->resolveRepository();

            $response = $envService->list($repo['workspace'], $repo['repo_slug']);
            $environments = array_map(fn (array $data) => Environment::fromApi($data), $response['values'] ?? []);

            $rows = array_map(fn (Environment $env) => [
                $env->uuid,
                $env->name,
                $env->slug,
                $env->type,
            ], $environments);

            $this->renderTable(['UUID', 'Name', 'Slug', 'Type'], $rows);

            return self::SUCCESS;
        });
    }
}
