<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Projects;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectsFactory extends Factory
{
    protected $model = Projects::class;

    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->unique()->words(2, true)),
            'description' => fake()->optional()->paragraph(),
            'status' => ProjectStatus::default(),
        ];
    }
}
