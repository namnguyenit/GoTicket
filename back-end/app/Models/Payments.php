<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'transaction_id',
        'amount',
        'payment_method',
        'status',
        'paid_at',
    ];

    public function booking(){
        return $this->belongsTo(Bookings::class, 'booking_id');
    }
}
