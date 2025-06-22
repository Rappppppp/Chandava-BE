<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'nickname' => $faker->userName,
                'birthdate' => $faker->date('Y-m-d', now()->subYears(18)),
                'contact_number' => '09' . $faker->numerify('#########'),
                'email' => $faker->unique()->safeEmail,
                'address' => $faker->address,
                'role' => 'user',
                'password' => Hash::make('user12345'), // same password for all
                'email_verified_at' => now(),
            ]);
        }
    }
}
