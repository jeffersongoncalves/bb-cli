<?php

namespace App\Enums;

enum PipelineState: string
{
    case Completed = 'COMPLETED';
    case Pending = 'PENDING';
    case Building = 'BUILDING';
    case Paused = 'PAUSED';
    case Halted = 'HALTED';

    public function color(): string
    {
        return match ($this) {
            self::Completed => 'green',
            self::Pending => 'yellow',
            self::Building => 'blue',
            self::Paused => 'gray',
            self::Halted => 'red',
        };
    }
}
