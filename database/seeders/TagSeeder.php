<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        // Matches the tag pills shown against tasks in the Figma dashboard.
        $tags = [
            'Design', 'UX', 'Backend', 'Bug', 'Billing',
            'Docs', 'API', 'Frontend', 'Mobile', 'DevOps',
            'CI/CD', 'Performance', 'Email', 'Marketing',
        ];

        foreach ($tags as $title) {
            Tag::firstOrCreate(['title' => $title], [
                'description' => null,
                'created_by' => null,
            ]);
        }
    }
}
