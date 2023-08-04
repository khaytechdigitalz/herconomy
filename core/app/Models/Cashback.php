<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashback extends Model
{
    protected $guarded = ['id'];

     

    public function seller()
    {
        return $this->belongsTo(Seller::class,'seller_id')->withDefault();
    }
 
    public function store()
    {
        return $this->belongsTo(Shop::class,'store_id')->withDefault();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    } 
}
