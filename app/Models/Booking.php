<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'room_id',
        'room_title',
        'location',
        'start_date',
        'end_date',
        'rent_amount',
        'booking_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roomDetails()
    {
        return $this->belongsTo(RoomDetails::class);
    }
    public static function getByUserId($userId)
    {
        return Booking::where('user_id', $userId)->get();
    }
    // public function payment()
    // {
    //     return $this->hasOne(Payment::class);
    // }
}
