<?php

namespace App\Http\Controllers\User;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(DashboardService $dashboardService): Response
    {
        return Inertia::render('Dashboard', [
            'statusOptions' => TaskStatus::options(),
            'priorityOptions' => TaskPriority::options(),
            'projectOptions' => app(ProjectService::class)->activeOptions(),
            'metrics' => $dashboardService->getDashboardMetrics()
        ]);
    }

    public function tasksData(Request $request, TaskService $taskService): JsonResponse
    {
        $query = $taskService->getTasksQuery(
            $request->only(['search', 'status', 'priority', 'project'])
        );

        return $taskService->getTasksDataTable($query);
    }
}
