<?php

namespace App\Commands\Pr;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\Services\PullRequestService;
use LaravelZero\Framework\Commands\Command;

class FilesCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pr:files
        {id : Pull request ID}
        {--project= : The repository (owner/repo)}';

    protected $description = 'List files changed in a pull request';

    public function handle(PullRequestService $prService): int
    {
        return $this->handleBitbucketErrors(function () use ($prService) {
            $repo = $this->resolveRepository();
            $prId = (int) $this->argument('id');

            $files = $prService->files($repo['workspace'], $repo['repo_slug'], $prId);

            $rows = array_map(fn (array $file) => [
                $file['status'] ?? '',
                $file['new']['path'] ?? $file['old']['path'] ?? '',
                ($file['lines_added'] ?? 0) . ' / ' . ($file['lines_removed'] ?? 0),
            ], $files);

            $this->renderTable(['Status', 'File', 'Added / Removed'], $rows);

            return self::SUCCESS;
        });
    }
}
