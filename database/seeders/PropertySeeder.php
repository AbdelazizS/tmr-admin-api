<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use App\Models\Property;
use App\Models\Amenity;
use Faker\Factory as Faker;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $amenityIds = Amenity::pluck('id')->toArray();

        Property::factory()
            ->count(10)
            ->create()
            ->each(function ($property) use ($amenityIds, $faker) {
                $property->amenities()->sync(
                    $faker->randomElements($amenityIds, rand(2, 4))
                );
                $property->features()->saveMany(
                    \App\Models\Feature::factory()->count(rand(3, 5))->make()
                );
                \App\Models\PropertyImage::factory()
                    ->count(rand(2, 5))
                    ->state(new Sequence(fn ($seq) => ['order' => $seq->index]))
                    ->create(['property_id' => $property->id]);
            });
    }
}
