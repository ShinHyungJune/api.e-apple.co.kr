<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\ProductReview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Point>
 */
class PointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $modelType = $this->faker->randomElement([ Order::class, ProductReview::class ]);
        $model = $modelType::query()->inRandomOrder()->first();
        //$deposits = $model->getDepositPoints() ?? [];
        if (empty($model)) return [];
        return [
            'user_id' => $model->user_id,       // 랜덤 사용자
            'model_type' => $modelType,
            'model_id' => $model->id,
            'deposit' => $this->faker->numberBetween(0, 20000),
            'withdrawal' => 0,
            'balance' => $this->faker->numberBetween(0, 20000),
            'description' =>  $this->faker->randomElement(['주문적립', '포토리뷰 작성', '리뷰 작성']),
        ];
    }
}
