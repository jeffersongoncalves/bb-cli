<?php

namespace App\Exceptions;

use RuntimeException;

class RepositoryNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Could not determine repository. Use --project=owner/repo or run from a git repository with a Bitbucket remote.')
    {
        parent::__construct($message);
    }
}
