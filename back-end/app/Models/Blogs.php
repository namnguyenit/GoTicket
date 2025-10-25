<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Thêm dòng này

class Blogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        // 'user_id', // <-- *** XÓA DÒNG NÀY ***
    ];

    /**
     * Thêm 'image_url' vào mỗi response JSON
     */
    protected $appends = ['image_url'];

    /**
     * Accessor để tạo image_url
     * tự động chạy khi model được serialize
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // Trả về URL đầy đủ của file
            // Hãy chắc chắn bạn đã chạy `php artisan storage:link`
            return Storage::url($this->image);
        }
        return null;
    }

    /* *** VÔ HIỆU HÓA QUAN HỆ NÀY ***
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    */

    public function blogTrips()
    {
        return $this->hasMany(BlogTrip::class, 'blog_id');
    }
}