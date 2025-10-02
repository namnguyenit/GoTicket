<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coaches extends Model
{
    protected $table = 'coaches';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'vehicle_id',
        'identifier',
        'coach_type',
        'total_seats',
    ];

    public function vehicle(){
        return $this->belongsTo(Vehicles::class, 'vehicle_id');
    }

    public function seats(){
        return $this->hasMany(Seats::class, 'coach_id');
    }

    public function trips(){
        return $this->belongsToMany(Trips::class, 'trip_coaches', 'coach_id', 'trip_id')
            ->withPivot('coach_order');
    }
}

