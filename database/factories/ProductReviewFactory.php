<?php

namespace Database\Factories;

use App\Models\OrderProduct;
use App\Models\ProductReview;
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
        $orderProduct = OrderProduct::inRandomOrder()->whereNotNull('user_id')->first(); // 랜덤 상품
        return [
            'user_id' => $orderProduct->user_id,       // 랜덤 사용자
            'order_id' => $orderProduct->order_id,
            'order_product_id' => $orderProduct->id,
            'product_id' => $orderProduct->product_id,
            'rating' => $this->faker->numberBetween(1, 5),
            'review' => $this->faker->paragraph,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (ProductReview $item) {
            $url = 'https://picsum.photos/510/300?random';
            $imageUrls = [
                asset('/images/samples/pexels-pixabay-161559.jpg'),
                asset('/images/samples/pexels-mali-102104.jpg'),
                asset('/images/samples/pexels-markusspiske-1343537.jpg')
            ];
            $url = collect($imageUrls)->random();

            $item->addMediaFromUrl($url) // 예제 이미지 URL
            ->toMediaCollection(ProductReview::IMAGES);
        });
    }

}
