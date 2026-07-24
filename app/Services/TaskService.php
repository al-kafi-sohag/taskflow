<?php

namespace App\Services;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\JsonResponse;

class TaskService
{
    public function createTask(array $data, ?array $files = null): Task
    {
        return DB::transaction(function () use ($data, $files): Task {
            $task = new Task();
            $task->title = $data['title'];
            $task->description = $data['description'] ?? null;
            $task->status = $data['status'] ?? TaskStatus::default();
            $task->priority = $data['priority'] ?? TaskPriority::default();
            $task->due_date = $data['due_date'] ?? null;
            $task->created_by = user()->id;
            $task->save();

            $this->syncAssignees($task, $data['assignee_ids'] ?? []);
            $this->syncTags($task, $data['tag_ids'] ?? []);
            $this->syncProjects($task, $data['project_ids'] ?? []);

            if ($files) {
                $this->attachMedia($task, $files);
            }

            return $task;
        }, config('app.db_transaction_attemps', 3));
    }

    public function updateTask(Task $task, array $data, ?array $files = null): Task
    {
        return DB::transaction(function () use ($task, $data, $files): Task {
            $task->title = $data['title'];
            $task->description = $data['description'] ?? null;
            $task->status = $data['status'] ?? $task->status;
            $task->priority = $data['priority'] ?? $task->priority;
            $task->due_date = $data['due_date'] ?? null;
            $task->save();

            $this->syncAssignees($task, $data['assignee_ids'] ?? []);
            $this->syncTags($task, $data['tag_ids'] ?? []);
            $this->syncProjects($task, $data['project_ids'] ?? []);

            if ($files) {
                $this->attachMedia($task, $files);
            }

            return $task->fresh(['assignees', 'tags', 'projects']);
        }, config('app.db_transaction_attemps', 3));
    }

    public function getTasksQuery(array $filters = []): Builder
    {
        // unchanged
        $query = Task::query()
            ->with(['assignees:id,name', 'tags:id,title', 'projects:id,title'])
            ->orderByDesc('created_at');

        if (! empty($filters['search'])) {
            $rawSearch = $filters['search'];
            $searchValue = is_array($rawSearch) ? ($rawSearch['value'] ?? '') : $rawSearch;
            $search = trim((string) $searchValue);
            $numericId = null;

            if (preg_match('/^(?:tf-)?0*(\d+)$/i', $search, $matches)) {
                $numericId = (int) $matches[1];
            }

            if ($search !== '') {
                $query->where(function (Builder $builder) use ($search, $numericId) {
                    $builder->where('title', 'like', "%{$search}%")
                        ->orWhereHas('tags', fn (Builder $tag) => $tag->where('title', 'like', "%{$search}%"));

                    if ($numericId !== null) {
                        $builder->orWhere('id', $numericId);
                    }
                });
            }
        }

        if (! empty($filters['project'])) {
            $projectSlug = $filters['project'];
            $query->whereHas('projects', fn (Builder $b) => $b->where('projects.slug', $projectSlug));
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== null) {
            if ($status = TaskStatus::tryFrom((int) $filters['status'])) {
                $query->where('status', $status->value);
            }
        }

        if (isset($filters['priority']) && $filters['priority'] !== '' && $filters['priority'] !== null) {
            if ($priority = TaskPriority::tryFrom((int) $filters['priority'])) {
                $query->where('priority', $priority->value);
            }
        }

        if (! empty($filters['assigned_to'])) {
            $userId = $filters['assigned_to'];
            $query->whereHas('assignees', fn (Builder $b) => $b->where('users.id', $userId));
        }

        return $query;
    }

    protected function syncAssignees(Task $task, array $userIds): void
    {
        $task->assignees()->sync(
            collect($userIds)->mapWithKeys(fn ($id) => [$id => ['created_by' => user()->id]])->all()
        );
    }

    protected function syncTags(Task $task, array $tagIds): void
    {
        $task->tags()->sync(
            collect($tagIds)->mapWithKeys(fn ($id) => [$id => ['created_by' => user()->id]])->all()
        );
    }

    protected function syncProjects(Task $task, array $projectIds): void
    {
        $task->projects()->sync(
            collect($projectIds)->mapWithKeys(fn ($id) => [$id => ['created_by' => user()->id]])->all()
        );
    }

    protected function attachMedia(Task $task, array $files): void
    {
        foreach ($files as $file) {
            try {
                $task->addMedia($file)
                    ->withCustomProperties(['field_name' => $task->id])
                    ->toMediaCollection('attachments');
            } catch (\Exception $e) {
                Log::error('Task attachment upload failed: '.$e->getMessage());
                throw new \Exception('Could not upload task attachment: '.$e->getMessage());
            }
        }
    }

    public function getTasksDataTable(Builder $query): JsonResponse
    {
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
            ->addColumn('project_list', fn ($task) => $task->projects->pluck('title')->values()->all())
            ->addColumn('assignee_list', fn ($task) => $task->assignees
                ->map(fn ($user) => ['id' => $user->id, 'name' => $user->name])
                ->values()
                ->all())
            ->filter(function () {
                //
            }, true)
            ->toJson();
    }
    public function deleteTask(Task $task): void
    {
        DB::transaction(function () use ($task): void {
            $task->deleted_by = user()->id;
            $task->save();
            $task->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    public function getMyActiveTasksCount(?int $userId = null): int
    {
        $userId ??= user()->id;

        if (! $userId) {
            return 0;
        }

        return Task::query()->whereHas('assignees', fn ($q) => $q->where('users.id', $userId))
            ->count();
    }
}
