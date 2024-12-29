<?php

namespace Database\Factories;

use App\Models\Sweetness;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sweetness>
 */
class SweetnessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fruit_name' => $this->faker->word(),
            'sweetness' => $this->faker->numberBetween(10, 20),
            'standard_sweetness' => $this->faker->numberBetween(20, 30),
            'standard_datetime' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Sweetness $item) {
            //$url = 'https://picsum.photos/510/300?random';
            $imageUrls = [
                asset('/images/samples/pexels-pixabay-161559.jpg'),
                asset('/images/samples/pexels-mali-102104.jpg'),
                asset('/images/samples/pexels-markusspiske-1343537.jpg')
            ];
            $url = collect($imageUrls)->random();

            $item->addMediaFromUrl($url) // 예제 이미지 URL
            ->toMediaCollection(Sweetness::IMAGES);
            /*$product->addMediaFromUrl($url) // 예제 이미지 URL
            ->toMediaCollection(Product::DESC_IMAGES);*/
        });
    }
}
