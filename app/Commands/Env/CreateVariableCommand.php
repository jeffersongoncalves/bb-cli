<?php

namespace App\Commands\Env;

use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\Services\EnvironmentService;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class CreateVariableCommand extends Command
{
    use InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'env:create-variable
        {environment : Environment UUID}
        {--key= : Variable key}
        {--value= : Variable value}
        {--secured : Mark as secured}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Create an environment variable';

    public function handle(EnvironmentService $envService): int
    {
        return $this->handleBitbucketErrors(function () use ($envService) {
            $repo = $this->resolveRepository();
            $envUuid = $this->argument('environment');

            $key = $this->option('key') ?? text(label: 'Variable key', required: true);
            $value = $this->option('value') ?? text(label: 'Variable value', required: true);
            $secured = $this->option('secured') || (! $this->option('key') && confirm(label: 'Secured?', default: false));

            $envService->createVariable($repo['workspace'], $repo['repo_slug'], $envUuid, $key, $value, $secured);
            $this->components->info("Variable '{$key}' created.");

            return self::SUCCESS;
        });
    }
}
