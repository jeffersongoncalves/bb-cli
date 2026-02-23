<?php

namespace App\Commands\Pr;

use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\Services\PullRequestService;
use LaravelZero\Framework\Commands\Command;

class MergeCommand extends Command
{
    use InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pr:merge
        {id : Pull request ID}
        {--strategy= : Merge strategy (merge_commit, squash, fast_forward)}
        {--close-source : Close source branch after merge}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Merge a pull request';

    public function handle(PullRequestService $prService): int
    {
        return $this->handleBitbucketErrors(function () use ($prService) {
            $repo = $this->resolveRepository();
            $prId = (int) $this->argument('id');

            $options = [];

            if ($strategy = $this->option('strategy')) {
                $options['merge_strategy'] = $strategy;
            }

            if ($this->option('close-source')) {
                $options['close_source_branch'] = true;
            }

            $prService->merge($repo['workspace'], $repo['repo_slug'], $prId, $options);
            $this->components->info("PR #{$prId} merged.");

            return self::SUCCESS;
        });
    }
}
