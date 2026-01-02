<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'user_id' => 1,
                'name' => 'Gaji',
                'type' => 'income',
                'description' => 'Gaji Bulanan',
            ],
            [
                'user_id' => 1,
                'name' => 'Jajan',
                'type' => 'expense',
                'description' => 'Jajanan',
            ],
            [
                'user_id' => 1,
                'name' => 'Pengeluaran Bulanan',
                'type' => 'expense',
                'description' => 'Pengeluaran Bulanan',
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
