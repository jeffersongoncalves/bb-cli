<?php

namespace App\Concerns;

use Closure;
use RuntimeException;

/**
 * Commands never prompt on their own, so scripts, CI and agents can't hang
 * on a question. Prompts only run when the caller passes --interactive.
 */
trait PromptsOnRequest
{
    protected function optionOrPrompt(string $option, Closure $prompt, mixed $default = null): mixed
    {
        $value = $this->option($option);

        if ($value !== null) {
            return $value;
        }

        if ($this->option('interactive')) {
            return $prompt();
        }

        if ($default === null) {
            throw new RuntimeException("Missing --{$option} (or pass --interactive to be prompted).");
        }

        return $default;
    }
}
