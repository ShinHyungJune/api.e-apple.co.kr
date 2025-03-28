<?php

namespace Database\Factories;

use App\Models\ExchangeReturn;
use App\Models\OrderProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExchangeReturn>
 */
class ExchangeReturnFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderProduct = OrderProduct::inRandomOrder()->whereNotNull('user_id')->first();

        return [
            "order_id" => $orderProduct->order_id,
            "order_product_id" => $orderProduct->id,
            "user_id" => $orderProduct->user_id,
            "type" => $this->faker->randomElement(ExchangeReturn::TYPES),
            "problem" => $this->faker->randomElement([
                '상품이 마음에 들지 않음', '더 저렴한 상품을 발견함',
                '다른 상품이 배송됨', '배송된 장소에 박스가 분실됨', '다른 주소로 배송됨',
                '상품의 구성품/부속품이 들어있지 않음', '상품이 설명과 다름', '상품이 파손되어 배송됨', '상품 결함/기능에 이상이 있음'
            ]),
            /*"change_of_mind" => $this->faker->randomElement(['상품이 마음에 들지 않음', '더 저렴한 상품을 발견함']),
            "delivery_issue" => $this->faker->randomElement(['다른 상품이 배송됨', '배송된 장소에 박스가 분실됨', '다른 주소로 배송됨']),
            "product_issue" => $this->faker->randomElement(['상품의 구성품/부속품이 들어있지 않음', '상품이 설명과 다름', '상품이 파손되어 배송됨', '상품 결함/기능에 이상이 있음']),*/
            "description" => $this->faker->paragraphs(3, true),
        ];
    }
}
