<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InventoryTransactionTypeEnum: string implements HasLabel, HasColor
{

    case IN = 'In';
    case OUT = 'Out';
    case TRANSFER = 'Transfer';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::IN => Color::Green,
            self::OUT => Color::Red,
            self::TRANSFER => Color::Blue,
        };
    }

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
