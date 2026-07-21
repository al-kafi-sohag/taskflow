<?php

namespace Database\Seeders;

use App\Models\Projects;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = ['Platform', 'Growth', 'Infrastructure', 'Design'];

        foreach ($projects as $title) {
            Projects::firstOrCreate(['title' => $title], [
                'description' => null,
                'created_by' => null,
            ]);
        }
    }
}
