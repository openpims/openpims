<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    use HasFactory;
    protected $table = 'standards';
    protected $primaryKey = 'standard_id';
    protected $fillable = [
        'standard',
        'description',
        'mapping',
        'checked',
        'disabled',
        'user_id',
    ];
}
