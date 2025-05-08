<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TableStatusEnum: string implements HasLabel, HasColor
{
    case AVAILABLE = 'Available';
    case BUSY = 'Busy';
    case RESERVED = 'Reserved';
    case CLOSED = 'Closed';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::AVAILABLE => Color::Green,
            self::BUSY => Color::Yellow,
            self::RESERVED => Color::Blue,
            self::CLOSED => Color::Red,
        };
    }

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
