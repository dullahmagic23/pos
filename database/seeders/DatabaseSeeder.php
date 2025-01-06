<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PaymentMethodSeeder::class,
         ExpenseCategorySeeder::class,
        DiscountTypeSeeder::class,
        DiscountSeeder::class,
        ProductKeySeeder::class,
        RoleSeeder::class
        ]);
//         User::factory(10)->create();
       $this->call(UnitsSeeder::class);
//
//        $user = User::factory()->create([
//            'name' => 'Abdilah Ramadhani',
//            'email' => 'abdi@pos.com',
//        ]);
    }
}
