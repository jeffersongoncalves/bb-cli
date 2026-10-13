<?php

namespace App\Commands\Pr;

use App\Concerns\FormatsOutput;
use App\Concerns\InteractsWithBitbucket;
use App\Concerns\PromptsOnRequest;
use App\Concerns\ResolvesRepository;
use App\Services\PullRequestService;
use LaravelZero\Framework\Commands\Command;
use RuntimeException;

use function Laravel\Prompts\text;
use function Laravel\Prompts\textarea;

class CreateCommand extends Command
{
    use FormatsOutput, InteractsWithBitbucket, PromptsOnRequest, ResolvesRepository;

    protected $signature = 'pr:create
        {source : Source branch}
        {destination? : Destination branch (defaults to main)}
        {--title= : Pull request title (defaults to the source branch)}
        {--description= : Pull request description (defaults to empty)}
        {--description-file= : Read the description from a file (use - for stdin)}
        {--interactive : Prompt for missing title and description}
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

            $title = $this->optionOrPrompt('title', fn () => text(
                label: 'Pull request title',
                default: $source,
                required: true,
            ), $source);

            $description = $this->readDescriptionFile() ?? $this->optionOrPrompt('description', fn () => textarea(
                label: 'Pull request description',
                default: '',
            ), '');

            $data = [
                'title' => $title,
                // Bitbucket rejects summary.markup ("extra keys not allowed"); raw is rendered as markdown.
                'summary' => ['raw' => $description],
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

    /**
     * Read --description-file (`-` = stdin). Multiline bodies can't go through
     * the Composer bb.bat proxy on Windows as an argument.
     */
    private function readDescriptionFile(): ?string
    {
        $path = $this->option('description-file');

        if ($path === null) {
            return null;
        }

        $contents = $path === '-' ? stream_get_contents(STDIN) : @file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read description file: {$path}");
        }

        return $contents;
    }
}
