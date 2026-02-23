<?php

namespace App\Services;

class EnvironmentService
{
    public function __construct(
        protected BitbucketService $bitbucket,
    ) {}

    public function list(string $workspace, string $repoSlug): array
    {
        return $this->bitbucket->get($workspace, $repoSlug, 'environments/');
    }

    public function variables(string $workspace, string $repoSlug, string $environmentUuid): array
    {
        return $this->bitbucket->paginate($workspace, $repoSlug, "deployments_config/environments/{$environmentUuid}/variables");
    }

    public function createVariable(string $workspace, string $repoSlug, string $environmentUuid, string $key, string $value, bool $secured = false): array
    {
        return $this->bitbucket->post($workspace, $repoSlug, "deployments_config/environments/{$environmentUuid}/variables", [
            'key' => $key,
            'value' => $value,
            'secured' => $secured,
        ]);
    }

    public function updateVariable(string $workspace, string $repoSlug, string $environmentUuid, string $variableUuid, string $value, bool $secured = false): array
    {
        return $this->bitbucket->put($workspace, $repoSlug, "deployments_config/environments/{$environmentUuid}/variables/{$variableUuid}", [
            'value' => $value,
            'secured' => $secured,
        ]);
    }
}
