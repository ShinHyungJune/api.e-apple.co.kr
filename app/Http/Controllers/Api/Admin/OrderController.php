<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use App\Models\User;

class OrderController extends ApiController
{
    /**
     * 테스트용 임시
     */
    public function test()
    {
        $user = User::findOrFail(2);
        $user->update(['email'=>'test@test.com', 'password' => '123456']);

        $product = Product::findOrFail(2);
        $product->update(['price' => 1000, 'delivery_fee' => 0]);
        $product->options()->update(['price' => 500, 'stock_quantity' => 1000]);

        return $this->respondSuccessfully($user);
    }
}
