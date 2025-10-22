<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    
    protected $table = 'users';

    
    protected $primaryKey = 'id';

    
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'role',
    ];

    protected $attributes = [
        'role' => 'customer',
    ];

    public $timestamps = true;

    protected $hidden = ['password'];

    public function bookings(){
        return $this->hasMany(Bookings::class, 'user_id');
    }

    public function reviews(){
        return $this->hasMany(Reviews::class, 'user_id');
    }

    public function blogs(){
        return $this->hasMany(Blogs::class, 'author_id');
    }

    public function vendor(){
        return $this->hasOne(Vendor::class, 'user_id');
    }

    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    
    public function getJWTCustomClaims()
    {
        return [];
    }
}
