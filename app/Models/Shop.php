<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = ['product_id', 'quantity','selling_price','unit_id','expiry_date','date','stock_id','user_id'];
}
