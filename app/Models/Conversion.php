<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversion extends Model
{
    use SoftDeletes;
    protected $fillable = ['product_id','from_unit_id', 'to_unit_id', 'quantity', 'ratio', 'result','created_by','updated_by'];
}
