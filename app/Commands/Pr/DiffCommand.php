<?php

namespace App\Commands\Pr;

use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\Services\PullRequestService;
use LaravelZero\Framework\Commands\Command;

class DiffCommand extends Command
{
    use InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pr:diff
        {id : Pull request ID}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Show pull request diff';

    public function handle(PullRequestService $prService): int
    {
        return $this->handleBitbucketErrors(function () use ($prService) {
            $repo = $this->resolveRepository();
            $prId = (int) $this->argument('id');

            $diff = $prService->diff($repo['workspace'], $repo['repo_slug'], $prId);
            $this->line($diff);

            return self::SUCCESS;
        });
    }
}
