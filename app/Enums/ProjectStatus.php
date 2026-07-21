<?php

namespace App\Enums;

enum ProjectStatus:int
{
    case ACTIVE = 1;
    case INACTIVE = 2;
    case PENDING = 3;
    case BLOCKED = 4;
    case DELETED = 5;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::PENDING => 'Pending',
            self::BLOCKED => 'Blocked',
            self::DELETED => 'Deleted',
        };
    }

    public function badge(): array
    {
        return match ($this) {
            self::ACTIVE => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-200', 'dot' => 'bg-green-500'],
            self::INACTIVE => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'dot' => 'bg-red-500'],
            self::PENDING => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200', 'dot' => 'bg-yellow-500'],
            self::BLOCKED => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'dot' => 'bg-red-500'],
            self::DELETED => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'dot' => 'bg-gray-500'],
        };
    }

    public static function default(): self
    {
        return self::ACTIVE;
    }

    public static function options(): array
    {
        return array_map(
            fn (self $case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'badge' => $case->badge(),
            ],
            self::cases(),
        );
    }
}
