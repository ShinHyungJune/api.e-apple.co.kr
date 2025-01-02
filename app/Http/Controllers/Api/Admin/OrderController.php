<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends ApiController
{
    /**
     * 테스트용 임시
     */
    public function test(Request $request)
    {
        if ($request->input('setup')) {
            $user = User::findOrFail(2);
            $user->update(['email' => 'test@test.com', 'password' => '123456', 'name' => '테스트', 'phone' => '01091767659']);

            $product = Product::findOrFail(2);
            $product->update(['price' => 1000, 'delivery_fee' => 0]);
            $product->options()->update(['price' => 500, 'stock_quantity' => 1000]);
        }

        if (
            $request->input('order_id') && $request->input('order_status')
        ) {
            Order::findOrFail($request->input('order_id'))
                ->update(['status' => $request->input('order_status')]);
        }

        if (
            $request->input('order_product_id') && $request->input('order_product_status')
        ) {
            OrderProduct::findOrFail($request->input('order_product_id'))
                ->update(['status' => $request->input('order_product_status')]);
        }


        return $this->respondSuccessfully();
    }
}
