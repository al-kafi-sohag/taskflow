<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\Projects;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function createProject(array $data): Projects
    {
        return DB::transaction(function () use ($data): Projects {
            $project = new Projects();
            $project->title = $data['title'];
            $project->description = $data['description'] ?? null;
            $project->status = $data['status'] ?? ProjectStatus::default();
            $project->created_by = user()->id;
            $project->save();

            return $project;
        }, config('app.db_transaction_attemps', 3));
    }

    public function updateProject(Projects $project, array $data): Projects
    {
        return DB::transaction(function () use ($project, $data): Projects {
            $project->title = $data['title'];
            $project->description = $data['description'] ?? null;
            $project->status = $data['status'] ?? $project->status;
            $project->updated_by = user()->id;
            $project->save();

            return $project->fresh();
        }, config('app.db_transaction_attemps', 3));
    }

    public function deleteProject(Projects $project): void
    {
        DB::transaction(function () use ($project): void {
            $project->deleted_by = user()->id;
            $project->save();
            $project->delete();
        }, config('app.db_transaction_attemps', 3));
    }

    public function activeOptions($allStatus = false): array
    {
        return Projects::query()
            ->where('status', $allStatus ? '!=' : '=', ProjectStatus::ACTIVE)
            ->orderBy('title')
            ->get(['id', 'title', 'slug'])
            ->map(fn ($project) => ['value' => $project->slug, 'label' => $project->title])
            ->values()
            ->all();
    }

    public function getActiveProjectsForSidebar(): array
    {
        return Projects::query()
            ->where('status', ProjectStatus::ACTIVE)
            ->withCount('tasks')
            ->orderBy('title')
            ->get(['id', 'title', 'slug'])
            ->map(fn ($project) => [
                'id' => $project->id,
                'slug' => $project->slug,
                'label' => $project->title,
                'count' => $project->tasks_count,
            ])
            ->values()
            ->all();
    }

    public function getProjectsOverview()
    {
        return Projects::query()
            ->withCount('tasks')
            ->with(['creator:id,name', 'updater:id,name'])
            ->orderBy('title')
            ->get()
            ->map(fn (Projects $project) => [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'description' => $project->description,
                'tasks_count' => $project->tasks_count,
                'status' => [
                    'value' => $project->status->value,
                    'name' => $project->status->name,
                    'label' => $project->status->label(),
                    'badge' => $project->status->badge(),
                ],
                'created_at' => optional($project->created_at)->format('M d, Y g:i A'),
                'created_by' => optional($project->creator)->name,
                'updated_at' => optional($project->updated_at)->format('M d, Y g:i A'),
                'updated_by' => optional($project->updater)->name,
            ]);
}
}
