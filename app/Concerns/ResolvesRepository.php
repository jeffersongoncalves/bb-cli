<?php

namespace App\Concerns;

use App\Services\GitService;

trait ResolvesRepository
{
    protected function resolveRepository(): array
    {
        $gitService = app(GitService::class);
        $project = $this->option('project');

        return $gitService->resolveRepository($project);
    }
}
