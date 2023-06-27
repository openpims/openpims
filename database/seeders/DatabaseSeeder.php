<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use App\Models\Category;
use App\Models\Consent;
use App\Models\Site;
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
        Account::create([
            'account' => 'portalix',
        ]);
        User::create([
            //'user' => 'portalix',
            'account_id' => 1,
            'email' => 'portalix@gmail.com',
            'password' => Hash::make('gehheim'),
        ]);
        Site::create([
            'site' => 'trustee.eu',
            //'account_id' => 1,
        ]);
        Category::create([
            'category' => 'necessary',
            'site_id' => 1,
        ]);
        Category::create([
            'category' => 'functional',
            'site_id' => 1,
        ]);
        Category::create([
            'category' => 'statistics',
            'site_id' => 1,
        ]);
        Category::create([
            'category' => 'marketing',
            'site_id' => 1,
        ]);
        //Consense::create([
        //    'user_id' => 1,
        //    'category_id' => 1,
        //]);
    }
}
