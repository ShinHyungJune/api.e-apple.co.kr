<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestUserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::find(1);

        //주문등록
        Order::factory()->count(10)
            ->state(['user_id' => $user->id, 'status' => OrderStatus::ORDER_COMPLETE])
            ->create();

        //내 리뷰
        ProductReview::factory()->count(10)
            ->state(function () use ($user) {
                $orderProduct = OrderProduct::inRandomOrder()->where('user_id', $user->id)->first(); // 랜덤 상품
                return [
                    'user_id' => $orderProduct->user_id,
                    'order_id' => $orderProduct->order_id,
                    'order_product_id' => $orderProduct->id,
                    'product_id' => $orderProduct->product_id,
                    'product_option_id' => $orderProduct->product_option_id,
                ];
            })
            ->create();

        //주문확정 -> 작성 가능한 리뷰
        Order::factory()->count(5)
            ->state(['user_id' => $user->id, 'status' => OrderStatus::PURCHASE_CONFIRM])
            ->create();

    }
}
