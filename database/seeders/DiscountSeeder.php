<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discounts = [
            [
                'name' => '10% Discount',
                'code' => '10OFF',
                'discount_type_id' => 1,
                'value' => 10,
                'status' => 'active',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
            ],
            [
                'name' => '20% Discount',
                'code' => '20OFF',
                'discount_type_id' => 1,
                'value' => 20,
                'status' => 'active',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
            ],
            [
                'name' => '30% Discount',
                'code' => '30OFF',
                'discount_type_id' => 1,
                'value' => 30,
                'status' => 'active',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
            ],
            [
                'name' => '40% Discount',
                'code' => '40OFF',
                'discount_type_id' => 1,
                'value' => 40,
                'status' => 'active',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
            ],
            [
                'name' => '50% Discount',
                'code' => '50OFF',
                'discount_type_id' => 1,
                'value' => 50,
                'status' => 'active',
                'start_date' => '2025-01-01',
                'end_date' => '2025-12-31',
            ],
        ];

        foreach ($discounts as $discount) {
            \App\Models\Discount::create($discount);
        }
    }
}
