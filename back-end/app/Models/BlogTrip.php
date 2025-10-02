<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogTrip extends Model
{
    protected $table = 'blog_trip';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = ['blog_id', 'trip_id'];
}
