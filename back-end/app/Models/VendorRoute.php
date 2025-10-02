<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorRoute extends Model
{
    protected $table = 'vendor_routes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'vendor_id',
        'route_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function vendor(){
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function route(){
        return $this->belongsTo(Routes::class, 'route_id');
    }

    public function trips(){
        return $this->hasMany(Trips::class, 'vendor_route_id');
    }

    public function stopsTemplate(){
        return $this->hasMany(VendorRouteStop::class, 'vendor_route_id');
    }
}
