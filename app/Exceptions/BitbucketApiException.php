<?php

namespace App\Exceptions;

use JeffersonGoncalves\LaravelZero\ApiClient\ApiException;

class BitbucketApiException extends ApiException
{
    /**
     * Pull the human-readable message out of a decoded Bitbucket error body.
     *
     * Bitbucket reports errors under "error.message" (or a plain "error"
     * string), which we prefix with "Bitbucket API error:" to match the
     * original CLI output. Transport-level failures arrive as a plain
     * "message" key and are passed through unprefixed.
     *
     * @param  array<string, mixed>  $body
     */
    protected static function extractMessage(array $body): string
    {
        if (isset($body['error']['message']) && is_string($body['error']['message'])) {
            return 'Bitbucket API error: '.$body['error']['message'];
        }

        if (isset($body['error']) && is_string($body['error'])) {
            return 'Bitbucket API error: '.$body['error'];
        }

        if (isset($body['message']) && is_string($body['message'])) {
            return $body['message'];
        }

        return '';
    }
}
