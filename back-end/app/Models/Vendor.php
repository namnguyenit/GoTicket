<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'vendors';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'company_name',
        'address',
        'status',
        'logo_url',
    ];


    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicles(){
        return $this->hasMany(Vehicles::class, 'vendor_id');
    }

    public function vendorRoutes(){
        return $this->hasMany(VendorRoute::class, 'vendor_id');
    }

    public function stops(){
        return $this->hasMany(Stops::class, 'vendor_id');
    }
}
