<?php

namespace App\Services;

use App\DTOs\Pipeline;

class PipelineService
{
    public function __construct(
        protected BitbucketService $bitbucket,
    ) {}

    public function get(string $workspace, string $repoSlug, string $pipelineUuid): array
    {
        return $this->bitbucket->get($workspace, $repoSlug, "pipelines/{$pipelineUuid}");
    }

    public function latest(string $workspace, string $repoSlug): ?array
    {
        $response = $this->bitbucket->get($workspace, $repoSlug, 'pipelines/', [
            'sort' => '-created_on',
            'pagelen' => 1,
        ]);

        $values = $response['values'] ?? [];

        return ! empty($values) ? $values[0] : null;
    }

    public function wait(string $workspace, string $repoSlug, string $pipelineUuid, ?callable $onPoll = null): Pipeline
    {
        while (true) {
            $data = $this->get($workspace, $repoSlug, $pipelineUuid);
            $pipeline = Pipeline::fromApi($data);

            if ($onPoll) {
                $onPoll($pipeline);
            }

            if ($pipeline->isFinished()) {
                return $pipeline;
            }

            sleep(2);
        }
    }

    public function run(string $workspace, string $repoSlug, string $branch): array
    {
        return $this->bitbucket->post($workspace, $repoSlug, 'pipelines/', [
            'target' => [
                'ref_type' => 'branch',
                'type' => 'pipeline_ref_target',
                'ref_name' => $branch,
            ],
        ]);
    }

    public function custom(string $workspace, string $repoSlug, string $branch, string $pattern): array
    {
        return $this->bitbucket->post($workspace, $repoSlug, 'pipelines/', [
            'target' => [
                'ref_type' => 'branch',
                'type' => 'pipeline_ref_target',
                'ref_name' => $branch,
                'selector' => [
                    'type' => 'custom',
                    'pattern' => $pattern,
                ],
            ],
        ]);
    }
}
