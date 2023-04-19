<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transcationNo',
        'accountHolder',
        'email',
        'amount',
    ];

    // public function booking()
    // {
    //     return $this->belongsTO(Booking::class);
    // }
}
