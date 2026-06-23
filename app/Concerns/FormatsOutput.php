<?php

namespace App\Concerns;

use JeffersonGoncalves\LaravelZero\Console\FormatsOutput as BaseFormatsOutput;

trait FormatsOutput
{
    use BaseFormatsOutput;

    /**
     * Bitbucket-specific state -> color map (pull requests and pipelines).
     *
     * @return array<string, string>
     */
    protected function stateColors(): array
    {
        return [
            'OPEN' => 'blue',
            'MERGED' => 'green',
            'DECLINED' => 'red',
            'SUPERSEDED' => 'gray',
            'COMPLETED' => 'green',
            'SUCCESSFUL' => 'green',
            'FAILED' => 'red',
            'STOPPED' => 'yellow',
            'PENDING' => 'yellow',
            'BUILDING' => 'blue',
            'PAUSED' => 'gray',
            'HALTED' => 'red',
        ];
    }
}
