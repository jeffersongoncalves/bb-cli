<?php

namespace App\Commands\Pr;

use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\Services\PullRequestService;
use LaravelZero\Framework\Commands\Command;

class UnrequestChangesCommand extends Command
{
    use InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pr:unrequest-changes
        {id : Pull request ID}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Remove change request from a pull request';

    public function handle(PullRequestService $prService): int
    {
        return $this->handleBitbucketErrors(function () use ($prService) {
            $repo = $this->resolveRepository();
            $prId = (int) $this->argument('id');

            $prService->unrequestChanges($repo['workspace'], $repo['repo_slug'], $prId);
            $this->components->info("Change request removed from PR #{$prId}.");

            return self::SUCCESS;
        });
    }
}
