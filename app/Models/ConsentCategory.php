<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'consent_category_id';

    protected $fillable = [
        'user_id',
        'site_id',
        'category',
        'consent_status', // Was: checked (1=accepted, 0=rejected, null=not_set)
        'consented_at', // GDPR Art. 7: Timestamp when user gave/changed consent
    ];

    protected $casts = [
        'consent_status' => 'boolean',
        'consented_at' => 'datetime',
    ];

    /**
     * Standard cookie categories aligned with Open Cookie Database
     * https://github.com/jkwakman/Open-Cookie-Database
     *
     * These are the 4 standard categories that cover all legal requirements.
     * 'functional' cookies don't require consent (TDDDG §25 Abs. 2).
     */
    public const CATEGORIES = [
        'functional' => [
            'name' => 'Technisch notwendig',
            'description' => 'Erforderlich für die Grundfunktionen der Website (Session, Login, Warenkorb, Sicherheit)',
            'always_active' => true,
            'legal_basis' => 'TDDDG §25 Abs. 2 - keine Einwilligung erforderlich',
        ],
        'personalization' => [
            'name' => 'Personalisierung',
            'description' => 'Passen Inhalte und Funktionen an Ihre Präferenzen an',
            'always_active' => false,
            'legal_basis' => 'TDDDG §25 Abs. 1 - Einwilligung erforderlich',
        ],
        'analytics' => [
            'name' => 'Statistik & Analyse',
            'description' => 'Helfen uns, die Nutzung der Website zu verstehen und zu verbessern',
            'always_active' => false,
            'legal_basis' => 'TDDDG §25 Abs. 1 - Einwilligung erforderlich',
        ],
        'marketing' => [
            'name' => 'Marketing & Werbung',
            'description' => 'Ermöglichen personalisierte Werbung, Retargeting und Social Media Integration',
            'always_active' => false,
            'legal_basis' => 'TDDDG §25 Abs. 1 - Einwilligung erforderlich',
        ],
    ];

    /**
     * Get the user that owns the consent category.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the site that this consent category belongs to.
     */
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id', 'site_id');
    }

    /**
     * Get category name in German
     */
    public function getCategoryNameAttribute()
    {
        return self::CATEGORIES[$this->category]['name'] ?? $this->category;
    }

    /**
     * Check if category is always active (functional cookies)
     */
    public function isAlwaysActive()
    {
        return self::CATEGORIES[$this->category]['always_active'] ?? false;
    }
}
