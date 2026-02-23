<?php

namespace App\Commands\Branch;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\DTOs\Branch;
use App\Services\BranchService;
use LaravelZero\Framework\Commands\Command;

class ListCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'branch:list
        {--project= : The repository (owner/repo)}';

    protected $description = 'List repository branches';

    public function handle(BranchService $branchService): int
    {
        return $this->handleBitbucketErrors(function () use ($branchService) {
            $repo = $this->resolveRepository();

            $response = $branchService->list($repo['workspace'], $repo['repo_slug']);
            $branches = array_map(fn (array $data) => Branch::fromApi($data), $response['values'] ?? []);

            $rows = array_map(fn (Branch $branch) => [
                $branch->name,
                $branch->hash ?? '-',
                $branch->authorUser ?? '-',
                $branch->date ? $this->formatDate($branch->date) : '-',
            ], $branches);

            $this->renderTable(['Name', 'Hash', 'Author', 'Date'], $rows);

            return self::SUCCESS;
        });
    }
}
