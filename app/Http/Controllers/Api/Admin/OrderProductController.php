<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\DeliveryCompany;
use App\Enums\OrderStatus;
use App\Exports\OrderProductsExport;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\OrderProductRequest;
use App\Http\Resources\OrderProductResource;
use App\Models\OrderProduct;
use App\Models\SMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OrderProductController extends ApiController
{
    public function init()
    {
        $deliveryCompanyItems = DeliveryCompany::getItems();
        return response()->json(compact('deliveryCompanyItems'));
    }

    public function index(Request $request)
    {
        $filters = $request->input('search');
        $items = OrderProduct::with(['order', 'product', 'productOption'])
            ->whereIn('status', OrderStatus::CAN_DELVERY_MANAGES)
            ->search($filters)
            ->latest()->paginate($request->get('itemsPerPage', 10));
        return OrderProductResource::collection($items);
    }

    public function update(OrderProductRequest $request, $id = null)
    {
        $data = $request->validated();
        
        // 배송중 상태로 변경되는 경우 SMS 발송 처리
        $isShippingStart = isset($data['status']) && $data['status'] === OrderStatus::DELIVERY->value;
        
        if ($id > 0) {
            $orderProduct = OrderProduct::where('status', OrderStatus::DELIVERY_PREPARING)->findOrFail($id);
            $orderProduct->update($data);
            
            // 배송 시작 SMS 발송
            if ($isShippingStart) {
                $this->sendShippingNotification($orderProduct);
            }
        }

        if (!empty($data['ids']) && count($data['ids']) > 0) {
            $ids = $data['ids'];
            unset($data['ids']);
            
            $orderProducts = OrderProduct::where('status', OrderStatus::DELIVERY_PREPARING)
                ->whereIn('id', $ids)
                ->get();
            
            OrderProduct::where('status', OrderStatus::DELIVERY_PREPARING)
                ->whereIn('id', $ids)
                ->update($data);
            
            // 배송 시작 SMS 발송 (일괄처리)
            if ($isShippingStart) {
                foreach ($orderProducts as $orderProduct) {
                    $this->sendShippingNotification($orderProduct);
                }
            }
        }

        return $this->respondSuccessfully();
    }
    
    /**
     * 배송 시작 알림 문자 발송
     */
    private function sendShippingNotification(OrderProduct $orderProduct)
    {
        try {
            // 관련 정보 로드 (이미 로드되어 있지 않은 경우에만)
            if (!$orderProduct->relationLoaded('order')) {
                $orderProduct->load(['order.user', 'product', 'productOption']);
            }
            $order = $orderProduct->order;
            
            // 수신자 전화번호 확인
            $phone = $order->buyer_phone ?? $order->user?->phone;
            if (!$phone) {
                return;
            }
            
            // 택배사 정보
            $deliveryCompanyName = '';
            if ($orderProduct->delivery_company) {
                $deliveryCompany = DeliveryCompany::tryFrom($orderProduct->delivery_company);
                $deliveryCompanyName = $deliveryCompany?->label() ?? $orderProduct->delivery_company;
            }
            
            // 상품명 구성
            $productName = $orderProduct->product->name;
            if ($orderProduct->productOption) {
                $productName .= ' (' . $orderProduct->productOption->name . ')';
            }
            
            // 메시지 구성
            $message = "안녕하세요 고객님, 열매나무를 이용해주셔서 감사합니다. 고객님의 상품이 아래와 같이 출고될 예정이니 참고 부탁드립니다.\n\n";
            $message .= "# 출고정보\n";
            $message .= "- 택배사 : {$deliveryCompanyName}\n";
            $message .= "- 운송장번호 : {$orderProduct->delivery_number}\n";
            $message .= "- 출고상품 : {$productName}";
            
            // SMS 발송
            $sms = new SMS();
            $result = $sms->send($phone, '열매나무 배송안내', $message);
            
            // 디버깅을 위한 로그
            \Log::info('SMS 발송 결과', [
                'phone' => $phone,
                'result' => $result instanceof \Illuminate\Http\JsonResponse ? $result->getData() : $result
            ]);
            
        } catch (\Exception $e) {
            // SMS 발송 실패 시 로그 기록 (비즈니스 로직은 계속 진행)
            \Log::error('배송 알림 SMS 발송 실패', [
                'order_product_id' => $orderProduct->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 개별 상품 취소
     */
    public function cancel(Request $request, $id)
    {
        // 취소사유 검증
        $request->validate([
            'cancel_reason' => 'required|string|max:500'
        ]);
        
        $orderProduct = OrderProduct::with(['order', 'product', 'productOption'])
            ->whereIn('status', OrderStatus::CAN_ORDER_CANCELS)
            ->findOrFail($id);

        if (!in_array($orderProduct->status, OrderStatus::CAN_ORDER_CANCELS)) {
            $m = '상품이 ' . implode(', ', OrderStatus::getCanOrderCancelValues()) . ' 상태일 때만 취소할 수 있습니다.';
            abort(response()->json(['message' => $m, 'errors' => ['order_product' => $m]], 403));
        }

        DB::transaction(function () use ($orderProduct, $request) {
            // 개별 상품 취소 처리
            $orderProduct->update([
                'status' => OrderStatus::CANCELLATION_COMPLETE,
                'cancel_reason' => $request->cancel_reason
            ]);
            
            // 재고 복원
            $orderProduct->productOption()->increment('stock_quantity', $orderProduct->quantity);
            
            // SMS 발송
            $this->sendOrderProductCancellationNotification($orderProduct, $request->cancel_reason);
        });

        return $this->respondSuccessfully(OrderProductResource::make($orderProduct));
    }

    /**
     * 개별 상품 취소 알림 SMS 발송
     */
    private function sendOrderProductCancellationNotification(OrderProduct $orderProduct, $cancelReason)
    {
        try {
            $order = $orderProduct->order;
            
            // 수신자 전화번호 확인
            $phone = $order->buyer_contact ?? $order->user?->phone;
            if (!$phone) {
                return;
            }
            
            // 상품명 구성
            $productName = $orderProduct->product->name;
            if ($orderProduct->productOption) {
                $productName .= ' (' . $orderProduct->productOption->name . ')';
            }
            
            // 메시지 구성
            $message = "[열매나무] 주문 취소 안내\n";
            $message .= "주문번호: {$order->merchant_uid}\n";
            $message .= "취소상품: {$productName}\n";
            $message .= "취소사유: {$cancelReason}\n";
            $message .= "문의: 010-xxxx-xxxx";
            
            // SMS 발송
            $sms = new SMS();
            $result = $sms->send($phone, '열매나무 주문취소 안내', $message);
            
            // 디버깅을 위한 로그
            \Log::info('개별 상품 취소 SMS 발송 결과', [
                'order_product_id' => $orderProduct->id,
                'phone' => $phone,
                'result' => $result instanceof \Illuminate\Http\JsonResponse ? $result->getData() : $result
            ]);
            
        } catch (\Exception $e) {
            // SMS 발송 실패 시 로그 기록 (비즈니스 로직은 계속 진행)
            \Log::error('개별 상품 취소 SMS 발송 실패', [
                'order_product_id' => $orderProduct->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new OrderProductsExport($request), 'order_products_' . date('Y-m-d_His') . '.xlsx');
    }

}
