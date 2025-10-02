<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seats extends Model
{
    protected $table = 'seats';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'coach_id',
        'seat_number',
    ];

    public function coach(){
        return $this->belongsTo(Coaches::class, 'coach_id');
    }

    public function trips(){
        return $this->belongsToMany(Trips::class, 'trip_seats', 'seat_id', 'trip_id')
            ->withPivot(['price', 'status']);
    }
}
