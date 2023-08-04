<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cahbackjoint extends Model
{
    protected $table = 'cash_back';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
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
