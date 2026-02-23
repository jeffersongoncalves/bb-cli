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
        {--reviewers= : Comma-separated list of reviewer usernames}
        {--close-source : Close source branch after merge}
        {--project= : The repository (owner/repo)}';

    protected $description = 'Create a pull request';

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
                'description' => $description,
                'source' => ['branch' => ['name' => $source]],
                'destination' => ['branch' => ['name' => $destination]],
                'close_source_branch' => $this->option('close-source'),
            ];

            if ($reviewers = $this->option('reviewers')) {
                $reviewerList = array_map(
                    fn ($r) => ['username' => trim($r)],
                    explode(',', $reviewers),
                );
                $data['reviewers'] = $reviewerList;
            }

            $result = $prService->create($repo['workspace'], $repo['repo_slug'], $data);

            $this->components->info("PR #{$result['id']} created: {$result['title']}");
            $this->components->twoColumnDetail('URL', $result['links']['html']['href'] ?? '');

            return self::SUCCESS;
        });
    }
}
