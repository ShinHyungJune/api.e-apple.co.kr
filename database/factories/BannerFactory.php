<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->sentence(),
            'url' => $this->faker->url(),
            'is_active' => true
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Banner $item) {
            //$url = 'https://picsum.photos/510/300?random';
            $imageUrls = [
                asset('/images/samples/pexels-pixabay-161559.jpg'),
                asset('/images/samples/pexels-mali-102104.jpg'),
                asset('/images/samples/pexels-markusspiske-1343537.jpg')
            ];
            $url = collect($imageUrls)->random();

            $item->addMediaFromUrl($url) // 예제 이미지 URL
            ->toMediaCollection(Banner::IMAGES);
            /*$product->addMediaFromUrl($url) // 예제 이미지 URL
            ->toMediaCollection(Product::DESC_IMAGES);*/
        });
    }

}
