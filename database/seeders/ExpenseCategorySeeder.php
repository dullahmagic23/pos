<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $expenseCategories = [
          ['name' => 'Operational Expenses'],
          ['name' => 'Personal Expenses'],
          ['name' => 'Marketing Expenses'],
          ['name' => 'Administrative Expenses'],
          ['name' => 'Research and Development (R&D) Expenses']
        ];

        foreach ($expenseCategories as $category){
            ExpenseCategory::create($category);
        }
    }
}
