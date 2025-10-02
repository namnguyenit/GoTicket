<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'id';
    public $timestamps = false; // only created_at column exists; disable auto-maintained timestamps

    protected $fillable = [
        'trip_id',
        'user_id',
        'booking_id',
        'rating',
        'comment',
        'created_at',
    ];

    protected $dates = ['created_at'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trip(){
        return $this->belongsTo(Trips::class, 'trip_id');
    }

    public function booking(){
        return $this->belongsTo(Bookings::class, 'booking_id');
    }
}
