<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wallets = [
            [
                'user_id' => 1,
                'name' => 'Cash',
                'balance' => 0,
            ],
            [
                'user_id' => 1,
                'name' => 'BCA',
                'balance' => 0,
            ],
            [
                'user_id' => 1,
                'name' => 'DANA',
                'balance' => 0,
            ],
        ];

        foreach ($wallets as $wallet) {
            \App\Models\Wallet::create($wallet);
        }
    }
}
