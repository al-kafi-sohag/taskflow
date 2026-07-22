<?php

namespace App\Services;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $task->created_by = auth()->id();
            $task->save();

            if (! empty($data['assignee_ids'])) {
                $this->syncAssignees($task, $data['assignee_ids']);
            }

            if (! empty($data['tag_ids'])) {
                $task->tags()->attach($data['tag_ids'], ['created_by' => auth()->id()]);
            }

            if (! empty($data['project_ids'])) {
                $task->projects()->attach($data['project_ids'], ['created_by' => auth()->id()]);
            }

            if ($files) {
                $this->attachMedia($task, $files);
            }

            return $task;
        }, config('app.db_transaction_attemps', 3));
    }
    public function getTasksQuery(array $filters = []): Builder
    {
        $query = Task::query()
            ->with([
                'assignees:id,name',
                'tags:id,title',
            ])
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

        return $query;
    }

    protected function syncAssignees(Task $task, array $userIds): void
    {
        $task->assignees()->attach($userIds, ['created_by' => auth()->id()]);
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
}
