<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingDetails extends Model
{
    protected $table = 'booking_details';
    public $timestamps = false;
    public $incrementing = false; 
    protected $primaryKey = null;

    protected $fillable = [
        'booking_id',
        'trip_id',
        'seat_id',
        'price_at_booking',
        'pickup_stop_id',
        'dropoff_stop_id',
    ];

    public function booking(){
        return $this->belongsTo(Bookings::class, 'booking_id');
    }

    public function tripSeat(){
        return $this->belongsTo(TripSeats::class, 'seat_id', 'seat_id')
            ->where('trip_id', $this->trip_id);
    }

    public function seat(){
        return $this->belongsTo(Seats::class, 'seat_id');
    }

    public function pickupStop(){
        return $this->belongsTo(Stops::class, 'pickup_stop_id');
    }

    public function dropoffStop(){
        return $this->belongsTo(Stops::class, 'dropoff_stop_id');
    }
    public function trip()
    {
    return $this->belongsTo(Trips::class, 'trip_id');
    }
}
