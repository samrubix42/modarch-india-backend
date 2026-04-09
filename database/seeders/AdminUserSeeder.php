<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed a default admin account for local development.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@modarch.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('modarch#@142@@ADMIN'),
                'email_verified_at' => now(),
            ]
        );
    }
}
