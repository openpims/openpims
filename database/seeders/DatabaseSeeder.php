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
            'standard' => 'Unbedingt erforderliche Cookies',
            'description' => 'Diese Cookies werden benötigt, damit Sie solche grundlegenden Funktionen wie Sicherheit, Identitätsprüfung und Netzwerkmanagement nutzen können. Sie können daher nicht deaktiviert werden.',
            'mapping' => 'necessary',
            'checked' => 1,
            'disabled' => 1,
        ]);
        Standard::create([
            'standard' =>'Cookies für Marketingzwecke',
            'description' =>'Cookies für Marketingzwecke werden verwendet, um die Effektivität von Werbung zu messen, Interessen von Besuchern zu erfassen und Werbeanzeigen an deren persönliche Bedürfnisse anzupassen.',
            'mapping' => 'marketing',
        ]);
        Standard::create([
            'standard' => 'Funktionale Cookies',
            'description' => 'Funktionale Cookies werden verwendet, um bereits getätigte Angaben zu speichern und darauf basierend verbesserte und personalisierte Funktionen anzubieten.',
            'mapping' => 'functional',
        ]);
        Standard::create([
            'standard' => 'Analytics-Cookies',
            'description' => 'Analytics-Cookies werden verwendet, um zu verstehen, wie Webseiten genutzt werden, um Fehler zu entdecken und Funktionalität von Webseiten zu verbessern.',
            'mapping' => 'analytics',
        ]);
        User::create([
            'email' => 'portalix@gmail.com',
            'password' => Hash::make('gehheim'),
        ]);
    }
}
