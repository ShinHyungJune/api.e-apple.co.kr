<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ExchangeReturnStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ExchangeReturnRequest;
use App\Http\Resources\ExchangeReturnResource;
use App\Models\ExchangeReturn;
use App\Models\Iamport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $wasCompletedBefore = $exchangeReturn->status === ExchangeReturnStatus::COMPLETED->value;
        $isCompletingNow = $data['status'] === ExchangeReturnStatus::COMPLETED->value && !$wasCompletedBefore;

        $exchangeReturn = DB::transaction(function () use ($exchangeReturn, $data, $isCompletingNow) {
            $exchangeReturn->update($data);
            if ($data['status'] === ExchangeReturnStatus::COMPLETED->value) {
                $exchangeReturn->processed_at = now();
                $exchangeReturn->save();

                if ($exchangeReturn->type === ExchangeReturn::TYPE_EXCHANGE) {
                    /* TODO CHECK 부분 교환일 경우 주문 상태는 어떻게?
                    $exchangeReturn->order()->update(['status' => OrderStatus::EXCHANGE_COMPLETE]);
                    */
                    $exchangeReturn->orderProduct()->update(['status' => OrderStatus::EXCHANGE_COMPLETE]);
                }

                if ($exchangeReturn->type === ExchangeReturn::TYPE_RETURN) {
                    if ($isCompletingNow && empty($exchangeReturn->auto_refund_succeeded_at)) {
                        $this->tryAutoRefund($exchangeReturn);
                    }

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

    /**
     * 반품 완료 시 아임포트 부분환불 자동 시도.
     * 실패해도 완료 처리는 진행 → 관리자가 입력해둔 환불계좌로 수동 입금하는 폴백.
     */
    private function tryAutoRefund(ExchangeReturn $exchangeReturn): void
    {
        if (!config('iamport.payment_integration')) {
            return;
        }

        $totalRefund = (int) ($exchangeReturn->refund_amount ?? 0) + (int) ($exchangeReturn->refund_delivery_fee ?? 0);
        if ($totalRefund <= 0) {
            return;
        }

        $order = $exchangeReturn->order()->first();
        if (!$order || empty($order->imp_uid)) {
            $exchangeReturn->forceFill(['auto_refund_error' => '결제정보(imp_uid)가 없어 자동환불 불가'])->save();
            return;
        }

        try {
            $accessToken = Iamport::getAccessToken();
            $result = Iamport::cancel($accessToken, $order->imp_uid, $totalRefund, '반품 환불');

            if (!empty($result['response'])) {
                $exchangeReturn->forceFill([
                    'auto_refund_succeeded_at' => now(),
                    'auto_refund_error' => null,
                ])->save();
            } else {
                $message = $result['message'] ?? '아임포트 환불 응답 비어있음';
                Log::warning('반품 자동환불 실패', [
                    'exchange_return_id' => $exchangeReturn->id,
                    'imp_uid' => $order->imp_uid,
                    'amount' => $totalRefund,
                    'message' => $message,
                ]);
                $exchangeReturn->forceFill(['auto_refund_error' => $message])->save();
            }
        } catch (\Throwable $e) {
            Log::error('반품 자동환불 예외', [
                'exchange_return_id' => $exchangeReturn->id,
                'error' => $e->getMessage(),
            ]);
            $exchangeReturn->forceFill(['auto_refund_error' => $e->getMessage()])->save();
        }
    }

}
