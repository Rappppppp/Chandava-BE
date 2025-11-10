<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Room;
use App\Models\MyBooking;
use App\Models\Feedback;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        DB::transaction(function () use ($faker) {

            /* -------------------------------------------------------------
             | USERS
             ------------------------------------------------------------- */
            $userData = [];
            for ($i = 0; $i < 10; $i++) {
                $userData[] = [
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'nickname' => $faker->userName,
                    'birthdate' => $faker->date('Y-m-d', '-18 years'),
                    'contact_number' => $faker->phoneNumber,
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('password'),
                    'address' => $faker->address,
                    'role' => 'user',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            User::insert($userData);
            $userIds = User::pluck('id')->toArray();

            /* -------------------------------------------------------------
             | ROOMS
             ------------------------------------------------------------- */
            $roomData = [];
            for ($i = 0; $i < 8; $i++) {
                $roomData[] = [
                    'room_name' => 'Room ' . ucfirst($faker->unique()->word()) . ' ' . sprintf('(%s)', Str::upper(Str::random(6))),
                    'accommodation_type_id' => 1,
                    'description' => $faker->sentence(10),
                    'day_night_tour_price' => $faker->randomFloat(2, 1000, 5000),
                    'overnight_price' => $faker->randomFloat(2, 2000, 8000),
                    'notes' => $faker->optional()->sentence(5),
                    'is_already_check_in' => $faker->boolean(20),
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Room::insert($roomData);
            $roomIds = Room::pluck('id')->toArray();

            /* -------------------------------------------------------------
             | BOOKINGS + FEEDBACKS
             ------------------------------------------------------------- */
            $bookings = [];
            $feedbacks = [];

            for ($i = 0; $i < 2000; $i++) {
                $year = $faker->numberBetween(2020, 2024);
                $checkIn = $faker->dateTimeBetween("$year-01-01", "$year-12-25");
                $checkOut = (clone $checkIn)->modify('+' . rand(1, 5) . ' days');
                $status = rand(1, 100) <= 80 ? 'completed' : 'cancelled';

                $userId = $faker->randomElement($userIds);
                $roomId = $faker->randomElement($roomIds);

                $bookings[] = [
                    'user_id' => $userId,
                    'room_id' => $roomId,
                    'total_price' => $faker->randomFloat(2, 2000, 15000),
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'tour_type' => $faker->randomElement(['Day Tour', 'Night Tour', 'Overnight']),
                    'status' => $status,
                    'receipt' => (string) Str::uuid() . '.png',
                    'admin_note' => $faker->optional()->sentence(8),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Add feedback for completed bookings
                if ($status === 'completed') {
                    $ratingChance = rand(1, 100);
                    $rate = match (true) {
                        $ratingChance <= 80 => 5,
                        $ratingChance <= 90 => 4,
                        default => rand(1, 3),
                    };

                    $feedbacks[] = [
                        'user_id' => $userId,
                        'room_id' => $roomId,
                        'rate' => $rate,
                        'comment' => $faker->sentence(rand(5, 15)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // SQL Server limit workaround
            foreach (array_chunk($bookings, 150) as $chunk) {
                MyBooking::insert($chunk);
            }

            foreach (array_chunk($feedbacks, 150) as $chunk) {
                Feedback::insert($chunk);
            }
        });
    }
}
