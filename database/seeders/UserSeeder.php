<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Known demo account for login testing / grading.
        User::firstOrCreate(
            ['email' => 'alex@taskflow.io'],
            [
                'name' => 'Alex Morgan',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $named = [
            ['name' => 'Sam Carter', 'email' => 'sam@taskflow.io'],
            ['name' => 'Jordan Lee', 'email' => 'jordan@taskflow.io'],
            ['name' => 'Riley Parker', 'email' => 'riley@taskflow.io'],
        ];

        foreach ($named as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }
        User::factory()->count(6)->create();
    }
}
