<?php

namespace App\Concerns;

use App\Exceptions\AuthenticationException;
use App\Exceptions\BitbucketApiException;
use App\Exceptions\RepositoryNotFoundException;

trait InteractsWithBitbucket
{
    protected function handleBitbucketErrors(callable $callback): int
    {
        try {
            return $callback();
        } catch (AuthenticationException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        } catch (BitbucketApiException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        } catch (RepositoryNotFoundException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
