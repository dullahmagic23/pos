<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $with = ['product', 'supplier', 'unit'];
    protected $fillable = [
        'product_id',
        'inventory_id',
        'supplier_id',
        'quantity',
        'unit_id',
        'user_id',
        'buying_price',
        'selling_price',
        'uuid',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
