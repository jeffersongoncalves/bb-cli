<?php

namespace App\Commands\Auth;

use App\Services\AuthService;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class SaveCommand extends Command
{
    protected $signature = 'auth:save';

    protected $description = 'Save Bitbucket credentials (username and API token)';

    public function handle(AuthService $authService): int
    {
        $username = text(
            label: 'Bitbucket username',
            required: true,
        );

        $apiToken = password(
            label: 'Bitbucket API token',
            required: true,
        );

        $authService->save($username, $apiToken);

        $this->components->info("Credentials saved to {$authService->getConfigPath()}");

        return self::SUCCESS;
    }
}
