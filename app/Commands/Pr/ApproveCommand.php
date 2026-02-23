<?php

namespace App\Commands\Pr;

use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\Services\PullRequestService;
use LaravelZero\Framework\Commands\Command;

class ApproveCommand extends Command
{
    use InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pr:approve
        {id* : Pull request ID(s), use 0 to approve all open PRs}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Approve pull request(s)';

    public function handle(PullRequestService $prService): int
    {
        return $this->handleBitbucketErrors(function () use ($prService) {
            $repo = $this->resolveRepository();
            $ids = $this->argument('id');

            if (in_array('0', $ids)) {
                $response = $prService->list($repo['workspace'], $repo['repo_slug'], ['state' => 'OPEN']);
                $ids = array_map(fn ($pr) => $pr['id'], $response['values'] ?? []);

                if (empty($ids)) {
                    $this->components->info('No open pull requests found.');

                    return self::SUCCESS;
                }
            }

            foreach ($ids as $id) {
                $prService->approve($repo['workspace'], $repo['repo_slug'], (int) $id);
                $this->components->info("PR #{$id} approved.");
            }

            return self::SUCCESS;
        });
    }
}
