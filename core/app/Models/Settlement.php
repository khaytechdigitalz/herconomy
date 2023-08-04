<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'settlement_information' => 'object'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class,'seller_id')->withDefault();
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function scopePending()
    {
        return $this->where('status', 2);
    }

    public function scopeApproved()
    {
        return $this->where('status', 1);
    }

    public function scopeRejected()
    {
        return $this->where('status', 3);
    }
}
