<?php

namespace Database\Seeders;

use App\Models\ProductKey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
class ProductKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keys = [
            ['key' => Crypt::encryptString('1234-5678-9000')],
            ['key' => Crypt::encryptString('0000-0000-0000')],
            ['key' => Crypt::encryptString('1111-1111-1111')],
            ['key' => Crypt::encryptString('2222-2222-2222')],
        ];
        foreach ($keys as $key){
            ProductKey::create($key);
        }
    }
}
