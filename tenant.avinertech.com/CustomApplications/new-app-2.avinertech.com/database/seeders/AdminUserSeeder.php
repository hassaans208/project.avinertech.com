<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First check if the user already exists
        $existingUser = User::where('email', 'admin@demo.com')->first();
        
        if (!$existingUser) {
            // Create admin user
            User::create([
                'name' => 'Admin',
                'email' => 'admin@demo.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Admin user created: admin@demo.com / admin123');
        } else {
            // Update the existing user's password
            $existingUser->password = Hash::make('admin123');
            $existingUser->save();
            
            $this->command->info('Admin user password updated: admin@demo.com / admin123');
        }
    }
}
