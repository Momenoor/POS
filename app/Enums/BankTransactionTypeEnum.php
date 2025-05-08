<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;

enum BankTransactionTypeEnum: string implements HasColor
{
    case DEPOSIT = 'Deposit';
    case WITHDRAWAL = 'Withdrawal';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DEPOSIT => Color::Green,
            self::WITHDRAWAL => Color::Red,
        };
    }
}
