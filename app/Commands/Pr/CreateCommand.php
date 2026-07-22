<?php

namespace App\Commands\Pr;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\ResolvesRepository;
use App\Services\PullRequestService;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\text;
use function Laravel\Prompts\textarea;

class CreateCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, ResolvesRepository;

    protected $signature = 'pr:create
        {source : Source branch}
        {destination? : Destination branch (defaults to main)}
        {--title= : Pull request title}
        {--description= : Pull request description}
        {--reviewers= : Comma-separated list of reviewer UUIDs}
        {--close-source : Close source branch after merge}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Create a pull request, or update it if one is already open for the source branch';

    public function handle(PullRequestService $prService): int
    {
        return $this->handleBitbucketErrors(function () use ($prService) {
            $repo = $this->resolveRepository();

            $source = $this->argument('source');
            $destination = $this->argument('destination') ?? 'main';

            $title = $this->option('title') ?? text(
                label: 'Pull request title',
                default: $source,
                required: true,
            );

            $description = $this->option('description') ?? textarea(
                label: 'Pull request description',
                default: '',
            );

            $data = [
                'title' => $title,
                'summary' => ['raw' => $description, 'markup' => 'markdown'],
                'source' => ['branch' => ['name' => $source]],
                'destination' => ['branch' => ['name' => $destination]],
                'close_source_branch' => $this->option('close-source'),
            ];

            if ($reviewers = $this->option('reviewers')) {
                $reviewerList = array_map(
                    fn ($r) => ['uuid' => trim($r)],
                    explode(',', $reviewers),
                );
                $data['reviewers'] = $reviewerList;
            }

            $existing = $prService->findOpenBySourceBranch($repo['workspace'], $repo['repo_slug'], $source);

            if ($existing) {
                $result = $prService->update($repo['workspace'], $repo['repo_slug'], $existing['id'], $data);
                $this->components->info("PR #{$result['id']} updated: {$result['title']}");
            } else {
                $result = $prService->create($repo['workspace'], $repo['repo_slug'], $data);
                $this->components->info("PR #{$result['id']} created: {$result['title']}");
            }

            $this->components->twoColumnDetail('URL', $result['links']['html']['href'] ?? '');

            return self::SUCCESS;
        });
    }
}
