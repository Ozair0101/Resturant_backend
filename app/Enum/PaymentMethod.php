<?php

namespace App\Enum;

enum PaymentMethod: string
{
    case CARD = "Card";
    case CASH = "Cash";

    public static function values(): array
    {
        return [
            self::CARD->value,
            self::CASH->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::CARD => 'Card',
            self::CASH => 'Cash',
        };
    }
}
