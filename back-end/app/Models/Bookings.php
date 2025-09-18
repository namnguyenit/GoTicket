<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    protected $table = 'bookings';


    protected $primaryKey = 'id';

    protected $fillable = ['booking_code',
                           'total_price',
                        'status'];


    public function user(){
        return belongsTo(User::class);
    }
}
