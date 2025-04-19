<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Property;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition()
    {
        return [
            'slug' => Str::slug($this->faker->unique()->sentence(3)) . '-' . $this->faker->randomNumber(5),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['available', 'sold', 'rented']),
            'type' => $this->faker->randomElement(['villa', 'apartment', 'townhouse', 'office']),
            'price' => $this->faker->randomFloat(2, 50000, 2000000),
            'bedrooms' => $this->faker->numberBetween(1, 6),
            'bathrooms' => $this->faker->numberBetween(1, 4),
            'area' => $this->faker->randomFloat(2, 50, 500),
            'district' => $this->faker->city(),
            'location' => $this->faker->city() . ', ' . $this->faker->country(),
            'address' => $this->faker->streetAddress(),
            'year_built' => $this->faker->year(),
            'video_tour' => null,
            'neighborhood' => [
                'description' => $this->faker->sentence(),
                'landmarks' => [$this->faker->word(), $this->faker->word()]
            ],
            'is_featured' => $this->faker->boolean(20),
        ];
    }
}
