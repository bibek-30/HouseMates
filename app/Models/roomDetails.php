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
        'user_id',
        'state',
        'zip',
        'price',
        'image',
        'desc',
        'latitude',
        'longitude',
        'address',
        'amenities',
        'conditions'


    ];
    protected $casts = [
        'amenities' => 'array',
        'conditions' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public static function getByUserId($userId)
    {
        return roomDetails::where('user_id', $userId)->get();
    }
}
