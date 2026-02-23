<?php

namespace App\Commands\Auth;

use App\Concerns\InteractsWithBitbucket;
use App\Exceptions\AuthenticationException;
use App\Services\AuthService;
use LaravelZero\Framework\Commands\Command;

class ShowCommand extends Command
{
    use InteractsWithBitbucket;

    protected $signature = 'auth:show';

    protected $description = 'Show saved Bitbucket credentials';

    public function handle(AuthService $authService): int
    {
        return $this->handleBitbucketErrors(function () use ($authService) {
            $credentials = $authService->load();

            if (! $credentials) {
                throw new AuthenticationException;
            }

            $this->components->twoColumnDetail('Username', $credentials->username);
            $this->components->twoColumnDetail('App Password', str_repeat('*', 8).substr($credentials->appPassword, -4));
            $this->components->twoColumnDetail('Config Path', $authService->getConfigPath());

            return self::SUCCESS;
        });
    }
}
