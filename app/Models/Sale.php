<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $with = ['customer','products','payments'];
    protected $fillable = [
        'uuid',
        'product_id',
        'customer_id',
        'unit_id',
        'date',
        'user_id'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot(['quantity','price','total','unit_id']);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getAmountAttribute()
    {
        $amount = 0;
        foreach ($this->products as $product){
            $amount = $amount + $product->pivot->amout;
        }
        return $amount;
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
