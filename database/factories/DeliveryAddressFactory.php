<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryAddress>
 */
class DeliveryAddressFactory extends Factory
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
            'name' => $this->faker->words(10, true),
            'recipient_name' => fake()->name(),
            'phone' => $this->faker->numerify('010#######'),
            'postal_code' => $this->faker->postcode,
            'address' => $this->faker->address,
            'address_detail' => $this->faker->optional()->secondaryAddress,
            'delivery_request' => $this->faker->optional()->sentence,
            'is_default' => $this->faker->boolean,
        ];
    }
}
