<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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

        
}
