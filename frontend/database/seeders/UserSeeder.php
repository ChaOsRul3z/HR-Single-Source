<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create an HR Admin User
        User::updateOrCreate(
            ['email' => 'admin@sdworx-hackathon.test'],
            [
                'name' => 'HR Administrator',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Create a Standard Employee User
        User::updateOrCreate(
            ['email' => 'employee@sdworx-hackathon.test'],
            [
                'name' => 'John Doe (Employee)',
                'role' => 'employee',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
