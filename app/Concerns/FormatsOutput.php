<?php

namespace App\Concerns;

trait FormatsOutput
{
    protected function renderTable(array $headers, array $rows): void
    {
        if (empty($rows)) {
            $this->components->info('No results found.');

            return;
        }

        $this->table($headers, $rows);
    }

    protected function stateColor(string $state): string
    {
        return match (strtoupper($state)) {
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
            default => 'white',
        };
    }

    protected function colorize(string $text, string $color): string
    {
        return "<fg={$color}>{$text}</>";
    }

    protected function formatDate(string $dateString): string
    {
        if (empty($dateString)) {
            return '';
        }

        try {
            $date = new \DateTime($dateString);

            return $date->format('Y-m-d H:i');
        } catch (\Exception) {
            return $dateString;
        }
    }
}
