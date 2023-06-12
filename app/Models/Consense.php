<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consense extends Model
{
    use HasFactory;
    protected $primaryKey = 'consense_id';
    protected $fillable = [
        'user_id',
        'host_id',
        'category_id',
    ];
}
