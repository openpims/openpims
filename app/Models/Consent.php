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
        'category_id',
        'cookie_id',
        'checked',
    ];
}
