<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@nunuca.nu'],
            [
                'name'              => 'Admin nunuca.nu',
                'password'          => Hash::make('nunuca2024!'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
