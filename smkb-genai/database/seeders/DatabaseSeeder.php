<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            ['password' => Hash::make('ubah-password-ini')]
        );

        $this->command->warn('User default: username=admin, password=ubah-password-ini — WAJIB diganti setelah login pertama.');
    }
}
