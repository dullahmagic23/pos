<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Piece'],
            ['name' => 'Dozen'],
            ['name' => 'Kilogram'],
            ['name' => 'Rim'],
            ['name' => '100 pcs Bundle'],
            ['name' => '50 pcs Bundle'],
            ['name' => 'Carton'],
            ['name' => 'Box'],
            ['name' => 'Set'],
            ['name' => 'Pair'],
            ['name' => 'Meter'],
            ['name' => 'Roll'],
            ['name' => 'Gallon'],
            ['name' => 'Pound'],
            ['name' => 'Inch'],
            ['name' => 'Foot'],
            ['name' => 'Yard'],
            ['name' => 'Centimeter'],
            ['name' => 'Millimeter'],
            ['name' => 'Liter'],
            ['name' => 'Piece'],
            ['name' => 'Liter'],
        ];

        foreach ($units as $unit) {
            \App\Models\Unit::create($unit);
        }
    }
}
