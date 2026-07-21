<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Projects;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function __construct(protected TaskService $tasks)
    {
    }

    public function run(): void
    {
        $users = User::all();
        $tags = Tag::all();
        $projects = Projects::all();

        if ($users->isEmpty()) {
            $this->command?->warn('No users found — run UserSeeder first. Skipping TaskSeeder.');

            return;
        }

        $fixedTasks = [
            ['title' => 'Redesign onboarding flow for new users', 'status' => TaskStatus::IN_PROGRESS, 'priority' => TaskPriority::HIGH, 'tags' => ['Design', 'UX']],
            ['title' => 'Fix authentication token refresh bug', 'status' => TaskStatus::IN_REVIEW, 'priority' => TaskPriority::URGENT, 'tags' => ['Backend', 'Bug']],
            ['title' => 'Implement Stripe billing integration', 'status' => TaskStatus::TO_DO, 'priority' => TaskPriority::HIGH, 'tags' => ['Backend', 'Billing']],
            ['title' => 'Write API documentation for v2 endpoints', 'status' => TaskStatus::DONE, 'priority' => TaskPriority::MEDIUM, 'tags' => ['Docs', 'API']],
            ['title' => 'Optimize database queries for task list', 'status' => TaskStatus::IN_PROGRESS, 'priority' => TaskPriority::HIGH, 'tags' => ['Backend', 'Performance']],
            ['title' => 'Build mobile-responsive navigation', 'status' => TaskStatus::TO_DO, 'priority' => TaskPriority::MEDIUM, 'tags' => ['Frontend', 'Mobile']],
            ['title' => 'Set up GitHub Actions CI/CD pipeline', 'status' => TaskStatus::DONE, 'priority' => TaskPriority::HIGH, 'tags' => ['DevOps', 'CI/CD']],
            ['title' => 'Create weekly digest email campaign', 'status' => TaskStatus::CANCELLED, 'priority' => TaskPriority::LOW, 'tags' => ['Email', 'Marketing']],
        ];

        foreach ($fixedTasks as $row) {
            $task = $this->tasks->createTask([
                'title' => $row['title'],
                'status' => $row['status'],
                'priority' => $row['priority'],
                'due_date' => now()->addDays(rand(-5, 14)),
                'assignee_ids' => [$users->random()->id],
                'tag_ids' => $tags->whereIn('title', $row['tags'])->pluck('id')->all(),
                'project_ids' => $projects->isNotEmpty() ? [$projects->random()->id] : [],
            ]);

            $this->command?->line("Seeded task: {$task->title}");
        }

        // Bulk filler tasks for pagination/filter testing.
        Task::factory()
            ->count(40)
            ->create(['created_by' => $users->random()->id])
            ->each(function (Task $task) use ($users, $tags, $projects) {
                $task->assignees()->attach(
                    $users->random(rand(1, 2))->pluck('id'),
                    ['created_by' => $users->random()->id]
                );

                if ($tags->isNotEmpty()) {
                    $task->tags()->attach(
                        $tags->random(rand(1, 3))->pluck('id'),
                        ['created_by' => $users->random()->id]
                    );
                }

                if ($projects->isNotEmpty()) {
                    $task->projects()->attach(
                        $projects->random()->id,
                        ['created_by' => $users->random()->id]
                    );
                }
            });
    }
}
