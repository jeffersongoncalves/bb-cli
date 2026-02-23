<?php

namespace App\Services;

class BrowseService
{
    public function getUrl(string $workspace, string $repoSlug): string
    {
        return "https://bitbucket.org/{$workspace}/{$repoSlug}";
    }

    public function open(string $url): void
    {
        $command = match (PHP_OS_FAMILY) {
            'Windows' => "start \"\" \"{$url}\"",
            'Darwin' => "open \"{$url}\"",
            default => "xdg-open \"{$url}\"",
        };

        exec($command);
    }
}
