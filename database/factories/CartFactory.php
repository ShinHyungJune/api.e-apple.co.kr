<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,       // 랜덤 사용자
            'product_id' => Product::inRandomOrder()->first()->id, // 랜덤 상품
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Cart $item) {
            $option = ProductOption::where('product_id', $item->product_id)->first();
            $item->cartProductOptions()->createMany([
                ['user_id' => $item->user_id, 'product_option_id' => $option->id, 'price' => $option->price, 'quantity' => $this->faker->numberBetween(1, 5)]
            ]);
        });
    }
}
