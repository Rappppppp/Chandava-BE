<?php

namespace Database\Seeders;

use App\Models\AccommodationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accomodationTypes = [
            [
                'accommodation_type_name' => 'Small Kubo',
                'max_guests' => 10,
            ],
            [
                'accommodation_type_name' => 'Big Kubo',
                'max_guests' => 20,
            ],
            [
                'accommodation_type_name' => 'Nippa Hut',
                'max_guests' => 8,
            ],
        ];
        
        AccommodationType::insert($accomodationTypes);
    }
}
