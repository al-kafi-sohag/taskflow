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
            $project->created_by = auth()->id();
            $project->save();

            return $project;
        }, config('app.db_transaction_attemps', 3));
    }
}
