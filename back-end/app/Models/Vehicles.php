<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model
{
    protected $table = 'vehicles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'vendor_id',
        'name',
        'vehicle_type',
        'license_plate',
    ];

    public $timestamps = true; // created_at, updated_at

    public function vendor(){
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function coaches(){
        return $this->hasMany(Coaches::class, 'vehicle_id');
    }
}
