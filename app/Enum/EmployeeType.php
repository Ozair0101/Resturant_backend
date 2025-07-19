<?php

namespace App\Enum;

enum EmployeeType: string
{
    case MANAGER = 'Manger';
    case STAFF = 'Staff';
    case CLEANER = 'Cleaner';
    case OWNER = 'Owner';

    public static function values(): array
    {
        return [
            self::MANAGER->value,
            self::STAFF->value,
            self::CLEANER->value,
            self::OWNER->value,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::MANAGER => 'Manger',
            self::STAFF => 'Staff',
            self::CLEANER => 'Cleaner',
            self::OWNER => 'Owner',
        };
    }
}
