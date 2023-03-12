<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    use HasFactory;
    protected $fillable = [
        'categories'
        // 'parent_id'
    ];

    protected $casts = [
        'categories' => 'array',
    ];
}
