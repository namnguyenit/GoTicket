<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripCoaches extends Model
{
    protected $table = 'trip_coaches';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null; // composite

    protected $fillable = [
        'trip_id', 
        'coach_id', 
        'coach_order'
    ];
}
