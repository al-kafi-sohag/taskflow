<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;

class DashboardService
{
    public function getDashboardMetrics(): array
    {
        $now = now();
        $oneMonthAgo = $now->copy()->subMonthNoOverflow();

        $totalNow = Task::count();
        $totalPast = Task::where('created_at', '<=', $oneMonthAgo)->count();

        $completedNow = Task::where('status', TaskStatus::DONE->value)->count();
        $completedPast = Task::where('status', TaskStatus::DONE->value)
            ->where('updated_at', '<=', $oneMonthAgo)
            ->count();

        $inProgressNow = Task::where('status', TaskStatus::IN_PROGRESS->value)->count();
        $inProgressPast = Task::where('status', TaskStatus::IN_PROGRESS->value)
            ->where('updated_at', '<=', $oneMonthAgo)
            ->count();

        $overdueNow = Task::whereNotIn('status', [TaskStatus::DONE->value, TaskStatus::CANCELLED->value])
            ->whereNotNull('due_date')
            ->where('due_date', '<', $now)
            ->count();

        $overduePast = Task::whereNotIn('status', [TaskStatus::DONE->value, TaskStatus::CANCELLED->value])
            ->whereNotNull('due_date')
            ->where('due_date', '<', $oneMonthAgo)
            ->where('created_at', '<=', $oneMonthAgo)
            ->count();

        return [
            'total' => $this->metric($totalNow, $totalPast),
            'completed' => $this->metric($completedNow, $completedPast),
            'in_progress' => $this->metric($inProgressNow, $inProgressPast),
            'overdue' => $this->metric($overdueNow, $overduePast),
        ];
    }


    protected function metric(int $current, int $previous): array
    {
        if ($previous <= 0) {
            $change = $current > 0 ? 100 : 0;
        } else {
            $change = (int) round((($current - $previous) / $previous) * 100);
        }

        return [
            'value' => $current,
            'change' => abs($change),
            'trend' => $change >= 0 ? 'up' : 'down',
        ];
    }
}
