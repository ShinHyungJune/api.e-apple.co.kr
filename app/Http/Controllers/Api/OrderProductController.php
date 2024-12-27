<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * 구매확정
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/order_product.json
     */
    public function confirm(Request $request, $id)
    {
        $orderProduct = OrderProduct::mine($request)->delivery()->findOrFail($id);
        $orderProduct = DB::transaction(function () use ($orderProduct) {
            $orderProduct->update(['status' => OrderStatus::PURCHASE_CONFIRM->value, 'purchase_confirmed_at' => now()]);
            /**
             * 모든 구매상품옵션이 confirm 되었을 때
             * 주문 상태도 업데이트
             * 적립금 부여
             */
            //TODO
            //$orderProduct->syncStatusOrderNDepositPoint();

            return $orderProduct;
        });

        return $this->respondSuccessfully(OrderProductResource::make($orderProduct));
    }
}
