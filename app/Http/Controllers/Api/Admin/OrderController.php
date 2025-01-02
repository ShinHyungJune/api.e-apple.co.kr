<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Order;
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
            $request->input('id') && $request->input('status')
        ) {
            Order::findOrFail($request->input('id'))
                ->update(['status' => $request->input('status')]);

        }


        return $this->respondSuccessfully($user);
    }
}
