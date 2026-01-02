<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Appearance;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            WalletSeeder::class,
            CategorySeeder::class,
        ]);

        $options = [
            'edited_by' => 1,
            'name_app' => "Cash Flow",
        ];

        Appearance::create($options);
    }
}
