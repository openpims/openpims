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
        'providers',
        'data_stored',
        'purposes',
        'retention_periods',
        'revocation_info',
    ];
}
