<?php

namespace Database\Factories;

use App\Enums\IamportMethod;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = User::inRandomOrder()->first()->id;

        return [
            'user_id' => $userId,
            'guest_id' => null,
            'status' => $this->faker->randomElement(OrderStatus::values()),
            'delivery_started_at' => null,
            'purchase_confirmed_at' => null,

            'buyer_name' => $this->faker->name(),
            'buyer_email' => $this->faker->safeEmail(),
            'buyer_contact' => $this->faker->phoneNumber(),
            'buyer_address_zipcode' => $this->faker->postcode(),
            'buyer_address' => $this->faker->address(),
            'buyer_address_detail' => $this->faker->secondaryAddress(),

            'delivery_name' => $this->faker->name(),
            'delivery_phone' => $this->faker->phoneNumber(),
            'delivery_postal_code' => $this->faker->postcode(),
            'delivery_address' => $this->faker->address(),
            'delivery_address_detail' => $this->faker->secondaryAddress(),
            'delivery_request' => $this->faker->sentence(),

            'common_entrance_method' => $this->faker->randomElement(['code', 'phone', 'none']),
            'total_amount' => $this->faker->numberBetween(1000, 100000),
            'user_coupon_id' => $this->faker->optional()->randomDigitNotNull(),
            'coupon_discount' => $this->faker->numberBetween(0, 10000),
            'use_points' => $this->faker->numberBetween(0, 5000),
            'delivery_fee' => $this->faker->numberBetween(0, 10000),
            'price' => $this->faker->numberBetween(1000, 100000),
            'imp_uid' => $this->faker->uuid(),
            //'merchant_uid' => $this->faker->uuid(),
            'merchant_uid' => 'ORDER20240000-' . $this->faker->numberBetween(100000, 999999),
            'payment_fail_reason' => $this->faker->optional()->sentence(),
            'is_payment_process_success' => $this->faker->boolean(),
            'is_payment_process_record' => $this->faker->boolean(),
            'pay_method_pg' => $this->faker->randomElement(['html5_inicis']),
            'pay_method_method' => $this->faker->randomElement(IamportMethod::values()),
            'vbank_num' => $this->faker->optional()->numerify('##############'),
            'vbank_name' => $this->faker->optional()->company(),
            'vbank_date' => $this->faker->optional()->dateTime(),
            'refund_bank_name' => $this->faker->optional()->company(),
            'refund_bank_owner' => $this->faker->optional()->name(),
            'refund_bank_account' => $this->faker->optional()->numerify('##############'),
            'refund_reason' => $this->faker->optional()->sentence(),
            'delivery_tracking_number' => $this->faker->optional()->numerify('##############'),
            'memo' => $this->faker->optional()->text(200),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $item) {
            $item->orderProducts()->createMany(
                [
                    [
                        'status' => $item->status,
                        'user_id' => $item->user_id,
                        'guest_id' => $item->guest_id,
                        'product_id' => 1,
                        'product_option_id' => 1,
                        'quantity' => $this->faker->numberBetween(0, 10),
                        'price' => $this->faker->numberBetween(1000, 100000),
                    ],
                ]
            );
        });
    }
}
