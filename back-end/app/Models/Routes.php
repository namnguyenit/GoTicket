<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routes extends Model
{
    protected $table = 'routes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'origin_location_id',
        'destination_location_id',
    ];

    // Mối quan hệ: Một tuyến đường có một điểm đi
    public function origin()
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    // Mối quan hệ: Một tuyến đường có một điểm đến
    public function destination()
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }
}