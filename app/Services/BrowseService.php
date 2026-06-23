<?php

namespace App\Services;

use JeffersonGoncalves\LaravelZero\Support\Browser;

class BrowseService
{
    public function getUrl(string $workspace, string $repoSlug): string
    {
        return "https://bitbucket.org/{$workspace}/{$repoSlug}";
    }

    public function open(string $url): bool
    {
        return Browser::open($url);
    }
}
