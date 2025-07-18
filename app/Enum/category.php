<?php

namespace App\Enum;

enum category: string
{
    case SHIRINI_BAB = "Shirini Bab";
    case KHURAKA_BAB = "Khuraka Bab";
    case NUSHABA_BAB = "Nushaba Bab";

    public static function values(): array
    {
        return [
            self::SHIRINI_BAB->value,
            self::KHURAKA_BAB->value,
            self::NUSHABA_BAB->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::NUSHABA_BAB => 'نوشابه باب',
            self::SHIRINI_BAB => 'شیرینی باب',
            self::KHURAKA_BAB => 'خوراکه باب',
        };
    }
}