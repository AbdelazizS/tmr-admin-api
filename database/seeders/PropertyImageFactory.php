<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PropertyImage;

class PropertyImageFactory extends Factory
{
    protected $model = PropertyImage::class;

    public function definition()
    {
        return [
            'path' => 'properties/' . $this->faker->image('storage/app/public/properties', 640, 480, null, false),
            'order' => 0,
        ];
    }
}
