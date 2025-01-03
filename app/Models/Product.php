<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'code', 'category_id'];
    protected $with = ['category','units','discounts'];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class)->withPivot(['quantity','price','expiry_date','added_date']);
    }

    public function sales()
    {
        return $this->belongsToMany(Sale::class)->withPivot(['quantity','price','total','unit_id']);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class)->withPivot(['discount_id','product_id']);
    }

}
