<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routes extends Model
{
    protected $table = 'routes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'origin_location',
        'destination_location',
    ];
}
