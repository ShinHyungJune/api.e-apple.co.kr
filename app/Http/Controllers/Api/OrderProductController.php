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
        $orderProduct = OrderProduct::with('order.orderProducts')->mine($request)->delivery()->findOrFail($id);
        if ($orderProduct->status !== OrderStatus::DELIVERY) {
            abort(403, '구매확정은 배송중에 가능합니다.');
        }
        $orderProduct = DB::transaction(function () use ($orderProduct) {
            $orderProduct->update(['status' => OrderStatus::PURCHASE_CONFIRM->value, 'purchase_confirmed_at' => now()]);

            //모든 구매상품옵션이 confirm 되었을 때 주문 상태도 업데이트
            $orderProduct->syncStatusOrder();

            //적립금 지급
            if (auth()->check()) {
                auth()->user()->depositPoint($orderProduct);
                list($amount) = $orderProduct->getDepositPoints();
                $orderProduct->order->increment('purchase_deposit_point', $amount);//구매확정으로 적립된 적립금
            }

            return $orderProduct;
        });

        return $this->respondSuccessfully(OrderProductResource::make($orderProduct));
    }
}
