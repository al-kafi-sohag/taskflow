<?php

namespace Database\Factories;

use App\Enums\TagStatus;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->unique()->word()),
            'description' => fake()->optional()->sentence(),
            'status' => TagStatus::default(),
        ];
    }
}
