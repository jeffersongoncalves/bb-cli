<?php

namespace App\Commands\Auth;

use App\Concerns\InteractsWithBitbucket;
use App\Concerns\PromptsOnRequest;
use App\DTOs\Credentials;
use App\Services\AuthService;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class SaveCommand extends Command
{
    use InteractsWithBitbucket, PromptsOnRequest;

    protected $signature = 'auth:save
        {--email= : Bitbucket account email}
        {--token= : Bitbucket API token}
        {--interactive : Prompt for missing email and token}';

    protected $description = 'Save Bitbucket credentials (email and API token)';

    public function handle(AuthService $authService): int
    {
        return $this->handleBitbucketErrors(function () use ($authService) {
            $username = $this->optionOrPrompt('email', fn () => text(
                label: 'Bitbucket account email',
                placeholder: 'your-email@example.com',
                required: true,
            ));

            $apiToken = $this->optionOrPrompt('token', fn () => password(
                label: 'Bitbucket API token',
                required: true,
            ));

            $authService->save(new Credentials($username, $apiToken));

            $this->components->info("Credentials saved to {$authService->getConfigPath()}");

            return self::SUCCESS;
        });
    }
}
