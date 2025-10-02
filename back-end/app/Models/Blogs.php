<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blogs extends Model
{
    protected $table = 'blogs';

    protected $primaryKey = 'id';


    protected $fillable = ['title',
                            'content',
                            'author_id',
                            'published_at'];

    public function author(){
        return $this->belongsTo(User::class, 'author_id');
    }

    public function trips(){
        return $this->belongsToMany(Trips::class, 'blog_trip', 'blog_id', 'trip_id');
    }

}
