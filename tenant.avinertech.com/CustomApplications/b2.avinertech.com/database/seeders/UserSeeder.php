<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main admin user
        // User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@demo.com',
        //     'password' => Hash::make('admin123'),
        //     'role' => 'admin',
        //     'status' => 'active',
        //     'email_verified_at' => now(),
        // ]);

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create manager user
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create regular users
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Test User $i",
                'email' => "user$i@example.com",
                'password' => Hash::make('password'),
                'role' => 'user',
                'status' => ($i % 3 == 0) ? 'inactive' : (($i % 2 == 0) ? 'pending' : 'active'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
