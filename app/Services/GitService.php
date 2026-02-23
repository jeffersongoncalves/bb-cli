<?php

namespace App\Services;

use App\Exceptions\RepositoryNotFoundException;

class GitService
{
    public function resolveRepository(?string $projectOption = null): array
    {
        if ($projectOption) {
            $parts = explode('/', $projectOption, 2);
            if (count($parts) !== 2) {
                throw new RepositoryNotFoundException("Invalid project format: {$projectOption}. Expected owner/repo.");
            }

            return ['workspace' => $parts[0], 'repo_slug' => $parts[1]];
        }

        return $this->detectFromGitRemote();
    }

    public function detectFromGitRemote(): array
    {
        $remoteUrl = $this->getRemoteUrl();

        return $this->parseRemoteUrl($remoteUrl);
    }

    public function getRemoteUrl(): string
    {
        $output = [];
        $exitCode = 0;
        exec('git remote get-url origin 2>&1', $output, $exitCode);

        if ($exitCode !== 0 || empty($output)) {
            throw new RepositoryNotFoundException('Not a git repository or no origin remote found.');
        }

        return trim($output[0]);
    }

    public function parseRemoteUrl(string $url): array
    {
        // SSH: git@bitbucket.org:owner/repo.git
        if (preg_match('#^git@bitbucket\.org:([^/]+)/([^/]+?)(?:\.git)?$#', $url, $matches)) {
            return ['workspace' => $matches[1], 'repo_slug' => $matches[2]];
        }

        // HTTPS: https://bitbucket.org/owner/repo.git
        if (preg_match('#^https?://(?:[^@]+@)?bitbucket\.org/([^/]+)/([^/]+?)(?:\.git)?$#', $url, $matches)) {
            return ['workspace' => $matches[1], 'repo_slug' => $matches[2]];
        }

        throw new RepositoryNotFoundException("Not a Bitbucket remote URL: {$url}");
    }

    public function getCurrentBranch(): ?string
    {
        $output = [];
        $exitCode = 0;
        exec('git rev-parse --abbrev-ref HEAD 2>&1', $output, $exitCode);

        if ($exitCode !== 0 || empty($output)) {
            return null;
        }

        return trim($output[0]);
    }
}
