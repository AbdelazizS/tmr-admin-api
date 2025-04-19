<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'Swimming Pool',],
            ['name' => 'Gym',],
            ['name' => 'Garage'],
            ['name' => 'Air Conditioning', ],
        ];

        foreach ($amenities as $data) {
            Amenity::create($data);
        }
    }
}
