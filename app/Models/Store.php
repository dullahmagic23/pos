<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $with = ['product','unit'];
    protected $fillable =['product_id','unit_id','stock_id','quantity','selling_price','buying_price','buying_price','expiry_date'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);

    }
}
