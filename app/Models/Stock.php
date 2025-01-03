<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['product_id', 'quantity','price','unit_id','expiry_date','date'];
    protected $with = ['product','unit'];

    public function product()
    {
        return $this->belongsTo(Product::class);

    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);

    }
}
