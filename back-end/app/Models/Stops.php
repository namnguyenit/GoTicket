<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stops extends Model
{
    protected $table = 'stops';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'vendori_id',
        'name',
        'address',
        'location_id',
    ];
}
