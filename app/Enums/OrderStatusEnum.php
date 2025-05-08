<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderStatusEnum: string implements HasLabel, HasColor
{
    case PENDING = 'Pending';
    case PREPARING = 'Preparing';
    case COMPLETED = 'Completed';
    case CANCELLED = 'Cancelled';
    case DELIVERED = 'Delivered';
    case RETURNED = 'Returned';
    case READY = 'Ready';
    case SERVED = 'Served';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => Color::Gray,
            self::PREPARING => Color::Yellow,
            self::COMPLETED, self::SERVED => Color::Green,
            self::CANCELLED, self::RETURNED => Color::Red,
            self::DELIVERED, self::READY => Color::Blue,
            default => null,
        };
    }

    public function getLabel(): ?string
    {
        return $this->value;
    }

    public static function getOperatingStatuses(): array
    {
        return [
            self::PENDING->value,
            self::PREPARING->value,
            self::SERVED->value,
            self::READY->value,
        ];
    }

    public static function getFailedStatuses(): array
    {
        return [
            self::CANCELLED->value,
            self::RETURNED->value,
        ];
    }
}
