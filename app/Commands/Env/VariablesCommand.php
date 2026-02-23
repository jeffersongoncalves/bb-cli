<?php

namespace App\Commands\Env;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\DTOs\EnvironmentVariable;
use App\Services\EnvironmentService;
use LaravelZero\Framework\Commands\Command;

class VariablesCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'env:variables
        {environment : Environment UUID}
        {--project= : The repository (owner/repo)}';

    protected $description = 'List environment variables';

    public function handle(EnvironmentService $envService): int
    {
        return $this->handleBitbucketErrors(function () use ($envService) {
            $repo = $this->resolveRepository();
            $envUuid = $this->argument('environment');

            $variables = $envService->variables($repo['workspace'], $repo['repo_slug'], $envUuid);
            $vars = array_map(fn (array $data) => EnvironmentVariable::fromApi($data), $variables);

            $rows = array_map(fn (EnvironmentVariable $var) => [
                $var->uuid,
                $var->key,
                $var->secured ? '********' : ($var->value ?? ''),
                $var->secured ? 'Yes' : 'No',
            ], $vars);

            $this->renderTable(['UUID', 'Key', 'Value', 'Secured'], $rows);

            return self::SUCCESS;
        });
    }
}
