<?php

namespace App\Exceptions;

use RuntimeException;

class BitbucketApiException extends RuntimeException
{
    public function __construct(
        string $message = 'Bitbucket API error.',
        int $code = 0,
        public readonly ?array $response = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function fromResponse(int $statusCode, array $body): self
    {
        $message = $body['error']['message'] ?? $body['error'] ?? "HTTP {$statusCode}";

        return new self("Bitbucket API error: {$message}", $statusCode, $body);
    }
}
