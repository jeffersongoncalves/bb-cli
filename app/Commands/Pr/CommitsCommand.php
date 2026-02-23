<?php

namespace App\Commands\Pr;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\Services\PullRequestService;
use LaravelZero\Framework\Commands\Command;

class CommitsCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pr:commits
        {id : Pull request ID}
        {--project= : The repository (owner/repo)}';

    protected $description = 'List commits in a pull request';

    public function handle(PullRequestService $prService): int
    {
        return $this->handleBitbucketErrors(function () use ($prService) {
            $repo = $this->resolveRepository();
            $prId = (int) $this->argument('id');

            $commits = $prService->commits($repo['workspace'], $repo['repo_slug'], $prId);

            $rows = array_map(fn (array $commit) => [
                substr($commit['hash'] ?? '', 0, 12),
                $commit['author']['user']['display_name'] ?? $commit['author']['raw'] ?? '',
                mb_substr(trim($commit['message'] ?? ''), 0, 60),
                $this->formatDate($commit['date'] ?? ''),
            ], $commits);

            $this->renderTable(['Hash', 'Author', 'Message', 'Date'], $rows);

            return self::SUCCESS;
        });
    }
}
