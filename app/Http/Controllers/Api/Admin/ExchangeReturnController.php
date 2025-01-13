<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ExchangeReturnStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ExchangeReturnRequest;
use App\Http\Resources\ExchangeReturnResource;
use App\Models\ExchangeReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeReturnController extends ApiController
{
    public function init(Request $request)
    {
        $statusItems = ExchangeReturnStatus::getItems();
        return $this->respondSuccessfully($statusItems);
    }

    public function index(Request $request)
    {
        $filters = $request->input('search');
        $items = ExchangeReturn::with(['orderProduct.product', 'order'])
            ->search($filters)
            ->latest()->paginate($request->get('itemsPerPage', 10));
        return ExchangeReturnResource::collection($items);
    }

    public function show(ExchangeReturn $exchangeReturn)
    {
        $exchangeReturn->load(['orderProduct.product', 'order']);
        return $this->respondSuccessfully(new ExchangeReturnResource($exchangeReturn));
    }


    /**
     *
     *
     * 교환/반품은 상품별로 이뤄짐 => 주문의 상태는 어떻게 처리??? => 부분교환, 부분반품 상태를 또 만들어야 하나???
     * 사용한 적립금, 포인트, 배송비 생각해서 관리자가 환불액 처리하기
     */
    public function update(ExchangeReturnRequest $request, ExchangeReturn $exchangeReturn)
    {
        $data = $request->validated();
        $exchangeReturn = DB::transaction(function () use ($exchangeReturn, $data) {
            $exchangeReturn->update($data);
            if ($data['status'] === ExchangeReturnStatus::COMPLETED->value) {
                $data['processed_at'] = now();
                if ($exchangeReturn->type === ExchangeReturn::TYPE_EXCHANGE) {
                    /* TODO CHECK 부분 교환일 경우 주문 상태는 어떻게?
                    $exchangeReturn->order()->update(['status' => OrderStatus::EXCHANGE_COMPLETE]);
                    */
                    $exchangeReturn->orderProduct()->update(['status' => OrderStatus::EXCHANGE_COMPLETE]);
                }

                if ($exchangeReturn->type === ExchangeReturn::TYPE_RETURN) {
                    $returnCompletedSum = ExchangeReturn::query()
                        ->selectRaw('SUM(refund_amount) AS sum_refund_amount, SUM(refund_delivery_fee) AS sum_refund_delivery_fee')
                        ->where([
                            'order_id' => $exchangeReturn->order_id,
                            'type' => ExchangeReturn::TYPE_RETURN,
                            'status' => ExchangeReturnStatus::COMPLETED->value
                        ])->first();
                    $exchangeReturn->order()->update([
                        'refund_amount_sum' => $returnCompletedSum->sum_refund_amount,
                        'refund_delivery_fee_sum' => $returnCompletedSum->sum_refund_delivery_fee,
                        //TODO CHECK 부분 환불일 경우 주문 상태는 어떻게?
                        // 'status' => OrderStatus::RETURN_COMPLETE
                    ]);

                    $exchangeReturn->orderProduct()->update(['status' => OrderStatus::RETURN_COMPLETE]);
                }
            }
            return $exchangeReturn;
        });
        return $this->respondSuccessfully(new ExchangeReturnResource($exchangeReturn));
    }

}
