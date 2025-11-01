<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users for passwordless authentication
        User::create([
            'email' => env('TEST_EMAIL', 'test@example.com'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'email' => env('TEST_EMAIL_EDGE', 'edge@example.com'),
            'email_verified_at' => now(),
        ]);
    }
}
