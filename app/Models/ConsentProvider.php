<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentProvider extends Model
{
    use HasFactory;

    protected $primaryKey = 'consent_provider_id';

    protected $fillable = [
        'user_id',
        'site_id',
        'category',
        'provider',
        'consent_status', // Was: checked (1=accepted, 0=rejected, null=not_set)
        'consented_at', // GDPR Art. 7: Timestamp when user gave/changed consent
    ];

    protected $casts = [
        'consent_status' => 'boolean',
        'consented_at' => 'datetime',
    ];

    /**
     * Provider name normalization map
     * Maps common variations to canonical names
     */
    public const PROVIDER_NORMALIZATION = [
        // Google
        'google analytics' => 'Google Analytics',
        'google inc.' => 'Google Analytics',
        'ga' => 'Google Analytics',
        'google ads' => 'Google Ads',
        'google llc' => 'Google LLC',

        // Meta/Facebook
        'facebook pixel' => 'Meta Platforms (Facebook)',
        'facebook' => 'Meta Platforms (Facebook)',
        'meta platforms inc.' => 'Meta Platforms (Facebook)',
        'meta' => 'Meta Platforms (Facebook)',

        // Twitter/X
        'twitter' => 'Twitter (X Corp.)',
        'twitter inc.' => 'Twitter (X Corp.)',
        'x corp.' => 'Twitter (X Corp.)',

        // Others
        'matomo' => 'Matomo',
        'hotjar' => 'Hotjar',
        'criteo' => 'Criteo',
    ];

    /**
     * Normalize provider name to canonical form
     */
    public static function normalizeProvider(string $provider): string
    {
        $lower = strtolower(trim($provider));

        // Check if we have a mapping
        if (isset(self::PROVIDER_NORMALIZATION[$lower])) {
            return self::PROVIDER_NORMALIZATION[$lower];
        }

        // Otherwise, just capitalize first letter of each word
        return ucwords($provider);
    }

    /**
     * Get the user that owns this consent provider.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the site that this consent provider belongs to.
     */
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id', 'site_id');
    }
}
