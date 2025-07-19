<?php

namespace App\Enum;

enum EmployeeStatus: string
{
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';


    public static function values(): array
    {
        return [
            self::ACTIVE->value,
            self::INACTIVE->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }
}
