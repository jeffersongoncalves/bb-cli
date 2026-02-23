<?php

namespace App\Commands\Pr;

use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\Services\PullRequestService;
use LaravelZero\Framework\Commands\Command;

class UnapproveCommand extends Command
{
    use InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pr:unapprove
        {id : Pull request ID}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Remove approval from a pull request';

    public function handle(PullRequestService $prService): int
    {
        return $this->handleBitbucketErrors(function () use ($prService) {
            $repo = $this->resolveRepository();
            $prId = (int) $this->argument('id');

            $prService->unapprove($repo['workspace'], $repo['repo_slug'], $prId);
            $this->components->info("PR #{$prId} approval removed.");

            return self::SUCCESS;
        });
    }
}
