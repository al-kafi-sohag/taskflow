<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Projects;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use App\Services\TagService;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Enums\ProjectStatus;
use App\Enums\TagStatus;
use App\Enums\UserStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function formOptions(): JsonResponse
    {
        return response()->json([
            'assignees' => User::query()
                ->where('status', UserStatus::ACTIVE)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
            'projects' => Projects::query()
                ->where('status', ProjectStatus::ACTIVE)
                ->select(['id', 'title'])
                ->orderBy('title')
                ->get(),
            'tags' => Tag::query()
                ->where('status', TagStatus::ACTIVE)
                ->select(['id', 'title'])
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function edit(Task $task): JsonResponse
    {
        $task->load(['assignees:id,name', 'projects:id,title', 'tags:id,title']);

        return response()->json([
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status->value,
            'priority' => $task->priority->value,
            'due_date' => optional($task->due_date)->format('Y-m-d'),
            'assignee_ids' => $task->assignees->pluck('id')->values()->all(),
            'project_ids' => $task->projects->pluck('id')->values()->all(),
            'tags' => $task->tags->pluck('title')->toArray(),
        ]);
    }

    public function store(TaskRequest $request, TaskService $taskService, TagService $tagService): RedirectResponse
    {
        $data = $this->prepareRelationIds($request->validated(), $tagService);

        $taskService->createTask($data);

        return back()->with('success', 'Task created successfully.');
    }

    public function update(TaskRequest $request, Task $task, TaskService $taskService, TagService $tagService): RedirectResponse
    {
        $data = $this->prepareRelationIds($request->validated(), $tagService);

        $taskService->updateTask($task, $data);

        return back()->with('success', 'Task updated successfully.');
    }

    protected function prepareRelationIds(array $data, TagService $tagService): array
    {
        $data['assignee_ids'] = ! empty($data['assignee_ids']) ? array_values($data['assignee_ids']) : [];
        $data['project_ids'] = ! empty($data['project_ids']) ? array_values($data['project_ids']) : [];
        $data['tag_ids'] = ! empty($data['tags']) ? $this->resolveTagIds($data['tags'], $tagService) : [];

        return $data;
    }

    protected function resolveTagIds(array $tags, TagService $tagService): array
    {
        return collect($tags)->map(fn ($title) => trim((string) $title))->filter()->unique(fn ($title) => strtolower($title))->map(function (string $title) use ($tagService) {
            $existing = Tag::query()
                ->whereRaw('LOWER(title) = ?', [strtolower($title)])
                ->first();
            return $existing?->id ?? $tagService->createTag(['title' => $title])->id;
        })->values()->all();
    }

    public function destroy(Task $task, TaskService $taskService): JsonResponse
    {
        $taskService->deleteTask($task);

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }

    public function show(Task $task): Response
    {
        $task->load(['assignees:id,name', 'projects:id,title', 'tags:id,title']);

        return Inertia::render('Tasks/Show', [
            'task' => [
                'id' => $task->id,
                'code' => sprintf('TF-%03d', $task->id),
                'title' => $task->title,
                'description' => $task->description,
                'status' => [
                    'value' => $task->status->value,
                    'name' => $task->status->name,
                    'label' => $task->status->label(),
                    'badge' => $task->status->badge(),
                ],
                'priority' => [
                    'value' => $task->priority->value,
                    'name' => $task->priority->name,
                    'label' => $task->priority->label(),
                    'badge' => $task->priority->badge(),
                ],
                'due_date' => optional($task->due_date)->format('Y-m-d'),
                'created_at' => optional($task->created_at)->format('Y-m-d'),
                'assignees' => $task->assignees->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])->values(),
                'projects' => $task->projects->map(fn ($p) => ['id' => $p->id, 'title' => $p->title])->values(),
                'tags' => $task->tags->pluck('title')->values(),
                'attachments_count' => $task->getMedia('attachments')->count(),
            ],
            'statusOptions' => TaskStatus::options(),
            'priorityOptions' => TaskPriority::options(),
        ]);
    }
}
