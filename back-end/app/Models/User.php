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

    /**
     * Tên bảng liên kết với model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Khóa chính của bảng.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Các thuộc tính có thể gán giá trị hàng loạt (mass assignable).
     *
     * @var array<int, string>
     */
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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
