<?php

namespace App\Services;

class BranchService
{
    public function __construct(
        protected BitbucketService $bitbucket,
    ) {}

    public function list(string $workspace, string $repoSlug, array $query = []): array
    {
        return $this->bitbucket->get($workspace, $repoSlug, 'refs/branches', $query);
    }

    public function listAll(string $workspace, string $repoSlug): array
    {
        return $this->bitbucket->paginate($workspace, $repoSlug, 'refs/branches');
    }

    public function filterByUser(string $workspace, string $repoSlug, string $username): array
    {
        $branches = $this->listAll($workspace, $repoSlug);

        return array_filter($branches, function (array $branch) use ($username) {
            $authorUser = $branch['target']['author']['user']['display_name']
                ?? $branch['target']['author']['raw']
                ?? '';

            return stripos($authorUser, $username) !== false;
        });
    }

    public function filterByName(string $workspace, string $repoSlug, string $pattern): array
    {
        return $this->bitbucket->paginate($workspace, $repoSlug, 'refs/branches', [
            'q' => "name ~ \"{$pattern}\"",
        ]);
    }
}
