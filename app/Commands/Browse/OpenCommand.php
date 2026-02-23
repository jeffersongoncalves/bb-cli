<?php

namespace App\Commands\Browse;

use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\Services\BrowseService;
use LaravelZero\Framework\Commands\Command;

class OpenCommand extends Command
{
    use InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'browse
        {--project= : The repository (owner/repo)}';

    protected $description = 'Open repository in browser';

    public function handle(BrowseService $browseService): int
    {
        return $this->handleBitbucketErrors(function () use ($browseService) {
            $repo = $this->resolveRepository();
            $url = $browseService->getUrl($repo['workspace'], $repo['repo_slug']);

            $browseService->open($url);
            $this->components->info("Opening {$url}");

            return self::SUCCESS;
        });
    }
}
