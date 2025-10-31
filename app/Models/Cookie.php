<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cookie extends Model
{
    use HasFactory;
    protected $primaryKey = 'cookie_id';
    protected $fillable = [
        'cookie',
        'site_id',
        'necessary',
        'category',
        'provider', // Singular (was: providers)

        // Open Cookie Database fields
        'domain',
        'is_third_party',
        'data_controller',
        'controller_country',
        'is_third_country',
        'is_wildcard',
        'pattern',
        'privacy_policy_url',

        // Existing fields
        'data_stored',
        'purposes',
        'retention_periods',
        'revocation_info',
    ];

    protected $casts = [
        'necessary' => 'boolean',
        'is_third_party' => 'boolean',
        'is_third_country' => 'boolean',
        'is_wildcard' => 'boolean',
    ];

    /**
     * Standard cookie categories aligned with Open Cookie Database
     * https://github.com/jkwakman/Open-Cookie-Database
     */
    public const CATEGORIES = [
        'functional' => 'Technisch notwendig',
        'personalization' => 'Personalisierung',
        'analytics' => 'Statistik & Analyse',
        'marketing' => 'Marketing & Werbung',
    ];
}
