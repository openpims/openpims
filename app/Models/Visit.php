<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;
    protected $primaryKey = 'visit_id';
    protected $fillable = [
        'user_id',
        'site_id',
        'updated_at',
    ];
}
