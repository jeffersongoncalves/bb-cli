<?php

namespace App\Commands\Browse;

use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\Services\BrowseService;
use LaravelZero\Framework\Commands\Command;

class ShowCommand extends Command
{
    use InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'browse:show
        {--project= : The repository (owner/repo)}';

    protected $description = 'Show repository URL without opening browser';

    public function handle(BrowseService $browseService): int
    {
        return $this->handleBitbucketErrors(function () use ($browseService) {
            $repo = $this->resolveRepository();
            $url = $browseService->getUrl($repo['workspace'], $repo['repo_slug']);

            $this->line($url);

            return self::SUCCESS;
        });
    }
}
