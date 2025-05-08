<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethodEnum: string implements HasColor, HasLabel, HasIcon
{
    case CASH = 'Cash';
    case CREDIT_CARD = 'Credit Card';
    case BANK_TRANSFER = 'Bank Transfer';
    case CHEQUE = 'Cheque';

    public function getColor(): string|array|null
    {
        return match ($this) {
            PaymentMethodEnum::CASH => Color::Green,
            PaymentMethodEnum::CREDIT_CARD => Color::Blue,
            PaymentMethodEnum::BANK_TRANSFER => Color::Yellow,
            PaymentMethodEnum::CHEQUE => Color::Red,
        };
    }

    public function getLabel(): ?string
    {
        return $this->value;
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            PaymentMethodEnum::CASH => 'heroicon-o-banknotes',
            PaymentMethodEnum::CREDIT_CARD => 'heroicon-o-credit-card',
            PaymentMethodEnum::BANK_TRANSFER => 'heroicon-o-arrow-path-rounded-square',
            PaymentMethodEnum::CHEQUE => 'heroicon-o-document-check',
        };
    }
}
