<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'vendors';

    protected $primaryKey = 'id';


    protected $fillable =['company_name',
                            'address',
                        'status',];


    public function user(){
        return $this->belongsTo(User::class);
    }
}
