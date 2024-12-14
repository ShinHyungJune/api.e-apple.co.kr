<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word, // 쿠폰 이름
            'type' => $this->faker->randomElement(Coupon::TYPES), // 할인 타입 ex) amount: 할인액, rate:
            'discount_amount' => $this->faker->numberBetween(1000, 10000), // 할인 금액
            'minimum_purchase_amount' => $this->faker->numberBetween(5000, 20000), // 최소 결제 금액
            'discount_rate' => $this->faker->numberBetween(0, 10), // 할인율 (0 ~ 1 사이)
            'usage_limit_amount' => $this->faker->numberBetween(10000, 100000), // 사용 한도 금액
            'valid_days' => $this->faker->numberBetween(7, 30), // 유효 일수
            'issued_until' => $this->faker->dateTimeBetween('+1 week', '+1 month'), // 발급 종료 일시
        ];
    }
}
