<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    protected $table = 'reviews';

    protected $fillable = ['rating'
                            ,'comment'
                            ,];

    public function users(){
        return $this->belongsTo(User::class);
    }
}
