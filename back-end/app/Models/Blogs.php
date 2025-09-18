<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blogs extends Model
{
    protected $table = 'blogs';

    protected $primaryKey = 'id';


    protected $fillable = ['title',
                            'content',
                            'published_at'];

    public function author(){
        return $this->belongsTo(User::class);
    }

}
