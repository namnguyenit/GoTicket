<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripStops extends Model
{
    protected $table = 'trip_stops';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null; // composite

    protected $fillable = [
        'trip_id', 
        'stop_id', 
        'stop_type', 
        'scheduled_time'
    ];
}
