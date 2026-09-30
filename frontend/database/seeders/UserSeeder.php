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
        User::firstOrCreate(
            ['email' => 'admin@sdworx-hackathon.test'],
            [
                'name' => 'HR Administrator',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Create a Standard Employee User
        User::firstOrCreate(
            ['email' => 'employee@sdworx-hackathon.test'],
            [
                'name' => 'John Doe (Employee)',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
