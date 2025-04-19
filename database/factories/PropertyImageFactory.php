<?php
namespace Database\Factories;

use App\Models\PropertyImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;

class PropertyImageFactory extends Factory
{
    protected $model = PropertyImage::class;

    public function definition(): array
{
    return [
        'path'  => 'properties/sample-image-' . $this->faker->numberBetween(1, 10) . '.jpg',
        'order' => $this->faker->numberBetween(0, 4),
    ];
}

}
