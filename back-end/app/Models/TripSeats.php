<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripSeats extends Model
{
    protected $table = 'trip_seats';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null; // composite

    protected $fillable = [
        'trip_id', 
        'seat_id', 
        'price', 
        'status'
    ];
}
