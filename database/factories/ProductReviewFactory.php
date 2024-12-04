<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductReview>
 */
class ProductReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::inRandomOrder()->first()->id, // 랜덤 상품
            'user_id' => User::inRandomOrder()->first()->id,       // 랜덤 사용자
            'rating' => $this->faker->numberBetween(1, 5),
            'review' => $this->faker->paragraph,
        ];
    }
}
