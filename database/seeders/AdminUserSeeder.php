<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::updateOrCreate(
            ['email' => 'admin@chandava.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'nickname' => 'admin',
                'birthdate' => now()->subYears(30)->toDateString(),
                'contact_number' => '09171234567',
                'address' => 'Admin Office',
                'role' => 'admin',
                'password' => Hash::make('admin123'), // 🔐 change this
                'email_verified_at' => now(),
            ]
        );
    }
}
