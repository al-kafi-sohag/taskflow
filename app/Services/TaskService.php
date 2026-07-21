<?php

namespace App\Services;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
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
