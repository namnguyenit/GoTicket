<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    protected $table = 'bookings';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'trip_id',
        'booking_code',
        'total_price',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trip(){
        return $this->belongsTo(Trips::class, 'trip_id');
    }

    public function details(){
        return $this->hasMany(BookingDetails::class, 'booking_id');
    }

    public function payment(){
        return $this->hasOne(Payments::class, 'booking_id');
    }
}
