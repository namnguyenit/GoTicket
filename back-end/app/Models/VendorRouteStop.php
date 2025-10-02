<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorRouteStop extends Model
{
    protected $table = 'vendor_route_stops';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'vendor_route_id',
        'stop_id',
        'stop_type',
        'stop_order',
        'offset_minutes_from_departure',
    ];

    public function vendorRoute(){
        return $this->belongsTo(VendorRoute::class, 'vendor_route_id');
    }

    public function stop(){
        return $this->belongsTo(Stops::class, 'stop_id');
    }
}
