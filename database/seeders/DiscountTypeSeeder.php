<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discountTypes = [
            ['name' => 'Percentage'],
            ['name' => 'Fixed'],
            ['name' => 'Free Shipping'],
            ['name' => 'Buy One Get One'],
            ['name' => 'Buy Two Get One'],
        ];

        foreach ($discountTypes as $discountType) {
            \App\Models\DiscountType::create($discountType);
        }
    }
}
