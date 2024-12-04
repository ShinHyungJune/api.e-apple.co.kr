<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductInquiry>
 */
class ProductInquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isAnswer = $this->faker->boolean();
        static $timestamp = null;
        if ($isAnswer) {
            // 초기 값 설정
            $timestamp = $timestamp ?? Carbon::now()->subYear();
            // 순차적으로 시간 증가
            $answeredAt = $timestamp->copy();
        }

        return [
            'product_id' => Product::inRandomOrder()->first()->id, // 랜덤 상품
            'user_id' => User::inRandomOrder()->first()->id,       // 랜덤 사용자
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(3, true),
            'is_visible' => $this->faker->boolean(),
            'answer' => $isAnswer ? $this->faker->sentence() : null,
            'answered_at' => $answeredAt ?? null,
        ];
    }
}
