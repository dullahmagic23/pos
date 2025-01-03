<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $with = ['orders','supplier'];
    protected $fillable = ['uuid','date','supplier_id'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
