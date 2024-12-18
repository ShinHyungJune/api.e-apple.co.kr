<?php

namespace Database\Factories;

use App\Enums\ProductCategory;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(10, true),
            'description' => $this->faker->paragraph,
            'view_count' => $this->faker->numberBetween(0, 10000),
            'price' => $this->faker->randomFloat(2, 1000, 50000),
            'original_price' => $this->faker->randomFloat(2, 5000, 60000),
            'delivery_fee' => $this->faker->randomFloat(2, 0, 5000),
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'categories' => collect(ProductCategory::cases())
                    ->random(rand(1, 2)) // 1~2개의 랜덤 값 선택
                    ->pluck('value')->toArray(),
            'is_md_suggestion_gift' => $this->faker->boolean,
            //'tags' => $this->faker->words(5), // JSON 배열로 저장
            'tags' => collect(['실시간 인기', '클래식 과일', '어른을 위한 픽', '추가 증정'])->random(2)->toArray(), // 2개의 랜덤 태그
            'food_type' => $this->faker->randomElement(['Vegetarian', 'Vegan', 'Non-Vegetarian']),
            'fruit_size' => $this->faker->randomElement(['Small', 'Medium', 'Large']),
            'sugar_content' => $this->faker->numberBetween(5, 30) . '%',
            'shipping_origin' => $this->faker->city,
            'manufacturer_and_location' => $this->faker->company . ', ' . $this->faker->address,
            'importer' => $this->faker->company,
            'origin' => $this->faker->country,
            'ingredients_and_composition' => $this->faker->sentence,
            'storage_and_handling' => $this->faker->sentence,
            'manufacture_date' => $this->faker->dateTimeBetween('-1 years', '-6 months')->format('Y-m-d'),
            'expiration_date' => $this->faker->dateTimeBetween('+1 months', '+2 years')->format('Y-m-d'),
            'gmo_desc' => $this->faker->boolean ? 'Contains GMO' : 'Non-GMO',
            'customer_service_contact' => $this->faker->phoneNumber,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            $url = 'https://picsum.photos/510/300?random';
            $imageUrls = [
                asset('/images/samples/pexels-pixabay-161559.jpg'),
                asset('/images/samples/pexels-mali-102104.jpg'),
                asset('/images/samples/pexels-markusspiske-1343537.jpg')
            ];
            $url = collect($imageUrls)->random();

            $product->addMediaFromUrl($url) // 예제 이미지 URL
            ->toMediaCollection(Product::IMAGES);
            /*$product->addMediaFromUrl($url) // 예제 이미지 URL
            ->toMediaCollection(Product::DESC_IMAGES);*/

            $product->options()->createMany(
                [
                    [
                        'name' => $this->faker->words(10, true),
                        'price' => $this->faker->randomFloat(2, 1000, 50000),
                        'stock_quantity' => $this->faker->numberBetween(0, 1000),
                    ],
                    [
                        'name' => $this->faker->words(10, true),
                        'price' => $this->faker->randomFloat(2, 1000, 50000),
                        'stock_quantity' => $this->faker->numberBetween(0, 1000),
                    ]
                ]
            );
        });
    }
}
