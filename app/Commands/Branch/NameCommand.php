<?php

namespace App\Commands\Branch;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\DTOs\Branch;
use App\Services\BranchService;
use LaravelZero\Framework\Commands\Command;

class NameCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'branch:name
        {pattern : Pattern to filter branch names}
        {--project= : The repository (owner/repo)}';

    protected $description = 'List branches matching a name pattern';

    public function handle(BranchService $branchService): int
    {
        return $this->handleBitbucketErrors(function () use ($branchService) {
            $repo = $this->resolveRepository();
            $pattern = $this->argument('pattern');

            $rawBranches = $branchService->filterByName($repo['workspace'], $repo['repo_slug'], $pattern);
            $branches = array_map(fn (array $data) => Branch::fromApi($data), $rawBranches);

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
