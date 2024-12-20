<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inquiry>
 */
class InquiryFactory extends Factory
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
            /*'purchase_related_inquiry' => $this->faker->randomElement([ '배송문의', '주문문의', '취소문의', '교환문의', '환불문의', '입금문의' ]),
            'general_consultation_inquiry' => $this->faker->randomElement(['회원정보', '결제문의', '상품문의', '쿠폰/마일리지', '기타']),*/
            'type' => $this->faker->randomElement([
                '배송문의', '주문문의', '취소문의', '교환문의', '환불문의', '입금문의',
                '회원정보', '결제문의', '상품문의', '쿠폰/마일리지', '기타'
            ]),
            'content' => $this->faker->paragraphs(3, true),
            'answer' => $this->faker->optional()->paragraphs(2, true),
            'answered_at' => $this->faker->optional()->dateTime,
        ];
    }
}
