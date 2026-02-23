<?php

namespace App\Services;

class PullRequestService
{
    public function __construct(
        protected BitbucketService $bitbucket,
    ) {}

    public function list(string $workspace, string $repoSlug, array $query = []): array
    {
        return $this->bitbucket->get($workspace, $repoSlug, 'pullrequests', $query);
    }

    public function get(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->get($workspace, $repoSlug, "pullrequests/{$prId}");
    }

    public function diff(string $workspace, string $repoSlug, int $prId): string
    {
        return $this->bitbucket->getRaw($workspace, $repoSlug, "pullrequests/{$prId}/diff");
    }

    public function files(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->paginate($workspace, $repoSlug, "pullrequests/{$prId}/diffstat");
    }

    public function commits(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->paginate($workspace, $repoSlug, "pullrequests/{$prId}/commits");
    }

    public function approve(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->post($workspace, $repoSlug, "pullrequests/{$prId}/approve");
    }

    public function unapprove(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->delete($workspace, $repoSlug, "pullrequests/{$prId}/approve");
    }

    public function requestChanges(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->post($workspace, $repoSlug, "pullrequests/{$prId}/request-changes");
    }

    public function unrequestChanges(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->delete($workspace, $repoSlug, "pullrequests/{$prId}/request-changes");
    }

    public function decline(string $workspace, string $repoSlug, int $prId): array
    {
        return $this->bitbucket->post($workspace, $repoSlug, "pullrequests/{$prId}/decline");
    }

    public function merge(string $workspace, string $repoSlug, int $prId, array $options = []): array
    {
        return $this->bitbucket->post($workspace, $repoSlug, "pullrequests/{$prId}/merge", $options);
    }

    public function create(string $workspace, string $repoSlug, array $data): array
    {
        return $this->bitbucket->post($workspace, $repoSlug, 'pullrequests', $data);
    }

    public function getDefaultReviewers(string $workspace, string $repoSlug): array
    {
        return $this->bitbucket->paginate($workspace, $repoSlug, 'default-reviewers');
    }

    public function getCurrentUser(): array
    {
        return $this->bitbucket->getCurrentUser();
    }
}
