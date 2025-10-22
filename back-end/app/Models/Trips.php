<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trips extends Model
{
    protected $table = 'trips';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'vendor_route_id',
        'departure_datetime',
        'arrival_datetime',
        'base_price',
        'status',
    ];

    protected $casts = [
        'departure_datetime' => 'datetime',
        'arrival_datetime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function vendorRoute(){
        return $this->belongsTo(VendorRoute::class, 'vendor_route_id');
    }

    public function coaches(){
        return $this->belongsToMany(Coaches::class, 'trip_coaches', 'trip_id', 'coach_id')
            ->withPivot('coach_order');
    }

    public function seats(){
        return $this->belongsToMany(Seats::class, 'trip_seats', 'trip_id', 'seat_id')
            ->withPivot(['price', 'status']);
    }

    public function stops(){
        return $this->belongsToMany(Stops::class, 'trip_stops', 'trip_id', 'stop_id')
            ->withPivot(['stop_type', 'scheduled_time']);
    }
}
