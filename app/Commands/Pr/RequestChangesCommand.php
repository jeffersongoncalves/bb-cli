<?php

namespace App\Commands\Pr;

use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\Services\PullRequestService;
use LaravelZero\Framework\Commands\Command;

class RequestChangesCommand extends Command
{
    use InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pr:request-changes
        {id : Pull request ID}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Request changes on a pull request';

    public function handle(PullRequestService $prService): int
    {
        return $this->handleBitbucketErrors(function () use ($prService) {
            $repo = $this->resolveRepository();
            $prId = (int) $this->argument('id');

            $prService->requestChanges($repo['workspace'], $repo['repo_slug'], $prId);
            $this->components->info("Changes requested on PR #{$prId}.");

            return self::SUCCESS;
        });
    }
}
