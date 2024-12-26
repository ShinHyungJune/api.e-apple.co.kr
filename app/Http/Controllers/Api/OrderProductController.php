<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

/**
 * @group Order(주문)
 */
class OrderProductController extends ApiController
{
    /**
     * 주문상품상세
     * @unauthenticated
     * @responseFile storage/responses/order_product.json
     */
    public function show(Request $request, $id)
    {
        $order = OrderProduct::with(['product'])->mine($request)->findOrFail($id);
        return $this->respondSuccessfully(OrderProductResource::make($order));
    }

}
