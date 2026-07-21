<?php

namespace App\Enums;
enum TaskStatus:int
{
    case TO_DO = 1;
    case IN_PROGRESS = 2;
    case IN_REVIEW = 3;
    case DONE = 4;
    case CANCELLED = 5;

    public function label(): string
    {
        return match ($this) {
            self::TO_DO => 'To Do',
            self::IN_PROGRESS => 'In Progress',
            self::IN_REVIEW => 'In Review',
            self::DONE => 'Done',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badge(): array
    {
        return match ($this) {
            self::TO_DO => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200'],
            self::IN_PROGRESS => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
            self::IN_REVIEW => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-200'],
            self::DONE => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200'],
            self::CANCELLED => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'border' => 'border-slate-200'],
        };
    }

    public function default(): TaskStatus
    {
        return self::TO_DO;
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
