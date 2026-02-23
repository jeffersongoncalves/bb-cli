<?php

namespace App\Commands\Pr;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\DTOs\PullRequest;
use App\Services\PullRequestService;
use LaravelZero\Framework\Commands\Command;

class ListCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pr:list
        {--state=OPEN : Filter by state (OPEN, MERGED, DECLINED, SUPERSEDED)}
        {--destination= : Filter by destination branch}
        {--project= : The repository (owner/repo)}';

    protected $description = 'List pull requests';

    public function handle(PullRequestService $prService): int
    {
        return $this->handleBitbucketErrors(function () use ($prService) {
            $repo = $this->resolveRepository();
            $query = ['state' => $this->option('state')];

            if ($destination = $this->option('destination')) {
                $query['q'] = "destination.branch.name = \"{$destination}\"";
            }

            $response = $prService->list($repo['workspace'], $repo['repo_slug'], $query);
            $pullRequests = array_map(fn (array $data) => PullRequest::fromApi($data), $response['values'] ?? []);

            $rows = array_map(fn (PullRequest $pr) => [
                $pr->id,
                $this->colorize($pr->state->value, $this->stateColor($pr->state->value)),
                mb_substr($pr->title, 0, 50),
                $pr->authorDisplayName,
                $pr->sourceBranch,
                $pr->destinationBranch,
                $this->formatDate($pr->updatedOn),
            ], $pullRequests);

            $this->renderTable(['ID', 'State', 'Title', 'Author', 'Source', 'Destination', 'Updated'], $rows);

            return self::SUCCESS;
        });
    }
}
