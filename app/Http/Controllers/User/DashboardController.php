<?php

namespace App\Http\Controllers\User;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'statusOptions' => TaskStatus::options(),
            'priorityOptions' => TaskPriority::options(),
        ]);
    }

    public function tasksData(Request $request, TaskService $taskService): JsonResponse
    {
        $query = $taskService->getTasksQuery(
            $request->only(['search', 'status', 'priority'])
        );

        return DataTables::eloquent($query)
            ->addColumn('code', fn ($task) => sprintf('TF-%03d', $task->id))
            ->editColumn('status', fn ($task) => [
                'value' => $task->status->value,
                'name' => $task->status->name,
                'label' => $task->status->label(),
                'badge' => $task->status->badge(),
            ])
            ->editColumn('priority', fn ($task) => [
                'value' => $task->priority->value,
                'name' => $task->priority->name,
                'label' => $task->priority->label(),
                'badge' => $task->priority->badge(),
            ])
            ->editColumn('due_date', fn ($task) => optional($task->due_date)->format('Y-m-d'))
            ->addColumn('attachments_count', fn ($task) => $task->getMedia('attachments')->count())
            ->addColumn('tag_list', fn ($task) => $task->tags->pluck('title')->values()->all())
            ->addColumn('assignee_list', fn ($task) => $task->assignees
                ->map(fn ($user) => ['id' => $user->id, 'name' => $user->name])
                ->values()
                ->all())
            ->filter(function () {
                //
            }, true)
            ->toJson();
    }
}
