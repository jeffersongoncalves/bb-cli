<?php

namespace App\Commands\Env;

use App\Concerns\InteractsWithBitbucket;
use App\Concerns\PromptsOnRequest;
use App\Concerns\ResolvesRepository;
use App\Services\EnvironmentService;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class UpdateVariableCommand extends Command
{
    use InteractsWithBitbucket, PromptsOnRequest, ResolvesRepository;

    protected $signature = 'env:update-variable
        {environment : Environment UUID}
        {--variable= : Variable UUID}
        {--value= : New variable value}
        {--secured : Mark as secured}
        {--interactive : Prompt for missing variable, value and secured flag}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Update an environment variable';

    public function handle(EnvironmentService $envService): int
    {
        return $this->handleBitbucketErrors(function () use ($envService) {
            $repo = $this->resolveRepository();
            $envUuid = $this->argument('environment');

            $variableUuid = $this->optionOrPrompt('variable', fn () => text(label: 'Variable UUID', required: true));
            $value = $this->optionOrPrompt('value', fn () => text(label: 'New value', required: true));
            $secured = $this->option('secured') || ($this->option('interactive') && confirm(label: 'Secured?', default: false));

            $envService->updateVariable($repo['workspace'], $repo['repo_slug'], $envUuid, $variableUuid, $value, $secured);
            $this->components->info('Variable updated.');

            return self::SUCCESS;
        });
    }
}
