<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class PaymetResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer' => $this->customer->name,
            'sale_amount' => DB::table('product_sale')->where('sale_id',$this->sle_id)->sum('total'),
            'amount_paid' => $this->sale->payments->sum('amount'),
            'amount_due'  => DB::table('product_sale')->where('sale_id',$this->sle_id)->sum('total') - $this->sale->payments->sum('amount'),

        ];
    }
}
