<?php

namespace App\Http\Controllers\User;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MyTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): Response
    {
        return Inertia::render('Tasks/MyTasks', [
            'statusOptions' => TaskStatus::options(),
            'priorityOptions' => TaskPriority::options(),
            'projectOptions' => app(ProjectService::class)->activeOptions(),
        ]);
    }

    public function tasksData(Request $request, TaskService $taskService): JsonResponse
    {
        $filters = $request->only(['search', 'status', 'priority', 'project']);
        $filters['assigned_to'] = user()->id;
        $query = $taskService->getTasksQuery($filters);

        return $taskService->getTasksDataTable($query);
    }
}
