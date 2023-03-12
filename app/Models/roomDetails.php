<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class roomDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'city',
        'available',
        'user_id',
        'state',
        'zip',
        'price',
        'image',
        'desc',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
