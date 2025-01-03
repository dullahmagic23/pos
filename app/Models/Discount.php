<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'name',
        'code',
        'discount_type_id',
        'value',
        'status',
        'start_date',
        'end_date',
    ];

    protected $with = ['discountType'];

    public function discountType()
    {
        return $this->belongsTo(DiscountType::class);
    }
}
