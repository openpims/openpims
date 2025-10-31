<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consent extends Model
{
    use HasFactory;
    protected $primaryKey = 'consent_id';
    protected $fillable = [
        'user_id',
        'cookie_id',
        'consent_status', // Was: checked (1=accepted, 0=rejected, null=not_set)
        'consented_at', // GDPR Art. 7: Timestamp when user gave/changed consent
    ];

    protected $casts = [
        'consent_status' => 'boolean',
        'consented_at' => 'datetime',
    ];
}
