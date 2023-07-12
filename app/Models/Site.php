<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    protected $primaryKey = 'site_id';
    protected $fillable = [
        'site',
        'url',
        'not_loaded',
    ];
}
