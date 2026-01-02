<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::create([
            'name' => 'Sulthan Alif Hayatyo',
            'email' => 'sulthanalif45@gmail.com',
            'password' => bcrypt('Sayanglala99'),
        ]);

        $superadmin->assignRole('superadmin');

        // Create 2 admin users
        // for ($i = 1; $i <= 2; $i++) {
        //     $admin = User::create([
        //         'name' => 'Admin ' . $i,
        //         'email' => 'admin' . $i . '@gmail.com',
        //         'password' => bcrypt('password'),
        //     ]);
        //     $admin->assignRole('admin');
        // }

        // Create 10 regular users
        // for ($i = 1; $i <= 10; $i++) {
        //     $user = User::create([
        //         'name' => 'User ' . $i,
        //         'email' => 'user' . $i . '@gmail.com',
        //         'password' => bcrypt('password'),
        //     ]);
        //     $user->assignRole('user');
        // }
    }
}
