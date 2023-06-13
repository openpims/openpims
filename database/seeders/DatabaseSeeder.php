<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Consense;
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
        User::create([
            //'user' => 'portalix',
            'email' => 'portalix@gmail.com',
            'password' => Hash::make('gehheim'),
        ]);

        User::create([
            //'user' => 'portalix2',
            'email' => 'portalix2@gmail.com',
            'password' => Hash::make('gehheim'),
        ]);

        Category::create(['category' => 'necessary']);
        Category::create(['category' => 'functional']);
        Category::create(['category' => 'statistics']);
        Category::create(['category' => 'marketing']);

        Consense::create([
            'category_id' => 1,
        ]);
        Consense::create([
            'user_id' => 2,
            'category_id' => 2,
        ]);
    }
}
