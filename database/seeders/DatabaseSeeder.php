<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use App\Models\Category;
use App\Models\Consent;
use App\Models\Site;
use App\Models\Standard;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Standard::create([#1
            'standard' => 'necessary',
            'checked' => 1,
            'disabled' => 1,
        ]);
        Standard::create([
            'standard' => 'functional',
        ]);
        Standard::create([
            'standard' => 'statistics',
        ]);
        Standard::create([
            'standard' => 'marketing',
        ]);
        User::create([
            'email' => 'portalix@gmail.com',
            'password' => Hash::make('gehheim'),
        ]);
    }
}
