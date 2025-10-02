<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model
{
    protected $table = 'vehicles';

    protected $primaryKey = 'id';


    protected $fillable =['name',
                            'vehicle_type',
                        ];

    

}
