<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'Swimming Pool','distance' => '1 km'],
            ['name' => 'Gym','distance' => '1 km'],
            ['name' => 'Garage','distance' => '1 km'],
            ['name' => 'Air Conditioning','distance' => '1 km'],
        ];

        foreach ($amenities as $data) {
            Amenity::create($data);
        }
    }
}
