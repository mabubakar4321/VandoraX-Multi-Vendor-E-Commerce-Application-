<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            [
                'email' => 'admin@vandorax.com',
            ],
            [
                'name' => 'Super Admin',
                'password' => 'Admin@12345',
                'phone_number' => '03001234567',
                'is_active' => true,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['admin']);

        $this->command->warn(
            'Admin: admin@vandorax.com / Admin@12345'
        );
    }
}