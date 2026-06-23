<?php

namespace App\Concerns;

use JeffersonGoncalves\LaravelZero\Console\HandlesApiErrors;

trait InteractsWithBitbucket
{
    use HandlesApiErrors;

    protected function handleBitbucketErrors(callable $callback): int
    {
        return $this->handleApiErrors($callback);
    }
}
