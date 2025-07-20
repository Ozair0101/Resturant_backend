<?php

namespace App\Enum;

enum orderStatus: string
{
    case PENDING = 'Pending';
    case PREPARING = 'Preparing';
    case SERVED = 'Served';
    case COMPLETED = 'Completed';

    public static function values(): array
    {
        return [
            self::PENDING->value,
            self::PREPARING->value,
            self::SERVED->value,
            self::COMPLETED->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PREPARING => 'Preparing',
            self::SERVED => 'Served',
            self::COMPLETED => 'Completed',
        };
    }
}
