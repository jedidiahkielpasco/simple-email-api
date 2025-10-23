<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('superadmin123'),
                'email_verified_at' => now(),
            ]
        );

        $jedidiah = User::firstOrCreate(
            ['email' => 'jedidiah@example.com'],
            [
                'name' => 'Jedidiah',
                'email' => 'jedidiah@example.com',
                'password' => Hash::make('jedidiah123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
