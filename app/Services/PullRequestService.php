<?php

namespace App\Services;

class PullRequestService
{
    public function __construct(
        protected BitbucketService $bitbucket,
    ) {}

    public function list(string $workspace, string $repoSlug, array $query = []): array
    {
        return $this->bitbucket->getRepo($workspace, $repoSlug, 'pullrequests', $query);
    }

    public function get(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->getRepo($workspace, $repoSlug, "pullrequests/{$prId}");
    }

    public function diff(string $workspace, string $repoSlug, int $prId): string
    {
        return $this->bitbucket->getRaw($workspace, $repoSlug, "pullrequests/{$prId}/diff");
    }

    public function files(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->paginateRepo($workspace, $repoSlug, "pullrequests/{$prId}/diffstat");
    }

    public function commits(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->paginateRepo($workspace, $repoSlug, "pullrequests/{$prId}/commits");
    }

    public function approve(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->postRepo($workspace, $repoSlug, "pullrequests/{$prId}/approve");
    }

    public function unapprove(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->deleteRepo($workspace, $repoSlug, "pullrequests/{$prId}/approve");
    }

    public function requestChanges(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->postRepo($workspace, $repoSlug, "pullrequests/{$prId}/request-changes");
    }

    public function unrequestChanges(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->deleteRepo($workspace, $repoSlug, "pullrequests/{$prId}/request-changes");
    }

    public function decline(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->postRepo($workspace, $repoSlug, "pullrequests/{$prId}/decline");
    }

    public function merge(string $workspace, string $repoSlug, int $prId, array $options = []): array
    {
        return $this->bitbucket->postRepo($workspace, $repoSlug, "pullrequests/{$prId}/merge", $options);
    }

    public function create(string $workspace, string $repoSlug, array $data): array
    {
        return $this->bitbucket->postRepo($workspace, $repoSlug, 'pullrequests', $data);
    }

    public function update(string $workspace, string $repoSlug, int $prId, array $data): array
    {
        return $this->bitbucket->putRepo($workspace, $repoSlug, "pullrequests/{$prId}", $data);
    }

    /**
     * Find the open pull request for a source branch, if one exists.
     */
    public function findOpenBySourceBranch(string $workspace, string $repoSlug, string $sourceBranch): ?array
    {
        $response = $this->list($workspace, $repoSlug, [
            'state' => 'OPEN',
            'q' => "source.branch.name = \"{$sourceBranch}\"",
        ]);

        return $response['values'][0] ?? null;
    }

    public function getDefaultReviewers(string $workspace, string $repoSlug): array
    {
        return $this->bitbucket->paginateRepo($workspace, $repoSlug, 'default-reviewers');
    }

    public function getCurrentUser(): array
    {
        return $this->bitbucket->getCurrentUser();
    }
}
