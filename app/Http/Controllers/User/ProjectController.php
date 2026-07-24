<?php

namespace App\Http\Controllers\User;

use App\Enums\ProjectStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Projects;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(ProjectService $projectService): Response
    {
        return Inertia::render('Projects/Index', [
            'projects' => $projectService->getProjectsOverview(),
            'statusOptions' => ProjectStatus::options(),
        ]);
    }

    public function show(Projects $project): Response
    {
        return Inertia::render('Projects/Show', [
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'description' => $project->description,
                'status' => [
                    'value' => $project->status->value,
                    'name' => $project->status->name,
                    'label' => $project->status->label(),
                    'badge' => $project->status->badge(),
                ],
            ],
            'statusOptions' => TaskStatus::options(),
            'priorityOptions' => TaskPriority::options(),
        ]);
    }

    public function edit(Projects $project): JsonResponse
    {
        return response()->json([
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'status' => $project->status->value,
        ]);
    }

    public function store(ProjectRequest $request, ProjectService $projectService): RedirectResponse
    {
        try {
            $projectService->createProject($request->validated());

            return back()->with('success', 'Project created successfully.');
        } catch (Throwable $e) {
            Log::error('Project creation failed: '.$e->getMessage(), ['exception' => $e]);

            return back()->with('error', 'Could not create the project. Please try again.')->withInput();
        }
    }

    public function update(ProjectRequest $request, Projects $project, ProjectService $projectService): RedirectResponse
    {
        try {
            $projectService->updateProject($project, $request->validated());

            return back()->with('success', 'Project updated successfully.');
        } catch (Throwable $e) {
            Log::error('Project update failed: '.$e->getMessage(), [
                'exception' => $e,
                'project_id' => $project->id,
            ]);

            return back()->with('error', 'Could not update the project. Please try again.')->withInput();
        }
    }

    public function destroy(Projects $project, ProjectService $projectService): JsonResponse
    {
        try {
            $projectService->deleteProject($project);

            return response()->json(['message' => 'Project deleted successfully.']);
        } catch (Throwable $e) {
            Log::error('Project deletion failed: '.$e->getMessage(), [
                'exception' => $e,
                'project_id' => $project->id,
            ]);

            return response()->json([
                'message' => 'Could not delete this project. Please try again.',
            ], 500);
        }
    }
    public function tasksData(Request $request, Projects $project, TaskService $taskService): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'status', 'priority']);
            $filters['project'] = $project->slug;

            $query = $taskService->getTasksQuery($filters);

            return $taskService->getTasksDataTable($query);
        } catch (Throwable $e) {
            Log::error('Project tasks fetch failed: '.$e->getMessage(), [
                'exception' => $e,
                'project_id' => $project->id,
            ]);

            return response()->json([
                'message' => 'Could not load tasks for this project.',
            ], 500);
        }
    }
}
