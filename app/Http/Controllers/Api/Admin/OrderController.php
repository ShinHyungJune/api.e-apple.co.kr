<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\DeliveryCompany;
use App\Enums\OrderStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\OrderResource;
use App\Models\Iamport;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

            if (
                $request->input('order_product_id') && $request->input('order_product_status')
            ) {
                OrderProduct::findOrFail($request->input('order_product_id'))
                    ->update(['status' => $request->input('order_product_status')]);
            } else {
                OrderProduct::where('order_id', $request->input('order_id'))
                    ->update(['status' => $request->input('order_status')]);
            }

        }

        // SMS 발송 테스트
        if ($request->input('test_sms')) {
            $this->testSmsDeliveryNotification();
            return $this->respondSuccessfully(['message' => 'SMS 테스트 발송 완료']);
        }

        return $this->respondSuccessfully();
    }
    
    /**
     * SMS 배송알림 테스트
     */
    private function testSmsDeliveryNotification()
    {
        // 테스트용 더미 데이터 생성 (DB 접속 없이)
        $orderProduct = new OrderProduct();
        $orderProduct->id = 999;
        $orderProduct->delivery_company = DeliveryCompany::CJ->value;
        $orderProduct->delivery_number = 'TEST123456789';
        $orderProduct->status = OrderStatus::DELIVERY_PREPARING->value;
        
        // 테스트용 주문 데이터
        $order = new Order();
        $order->id = 999;
        $order->buyer_phone = '01030217486';
        $order->buyer_name = '테스트고객';
        
        // 테스트용 상품 데이터
        $product = new Product();
        $product->name = '테스트 사과';
        
        $productOption = new ProductOption();
        $productOption->name = '10kg';
        
        // 관계 설정 (테스트용)
        $orderProduct->setRelation('order', $order);
        $orderProduct->setRelation('product', $product);
        $orderProduct->setRelation('productOption', $productOption);
        
        // OrderProductController의 sendShippingNotification 호출
        $orderProductController = new \App\Http\Controllers\Api\Admin\OrderProductController();
        
        // 리플렉션을 사용하여 private 메서드 호출
        $reflection = new \ReflectionClass($orderProductController);
        $method = $reflection->getMethod('sendShippingNotification');
        $method->setAccessible(true);
        $method->invoke($orderProductController, $orderProduct);
    }

    public function index(Request $request)
    {
        $filters = $request->input('search');
        $items = Order::with(['user', 'orderProducts.productOption'])
            //->where('status', '!=', OrderStatus::ORDER_PENDING->value)
            ->whereNotIn('status', [
                OrderStatus::ORDER_PENDING->value,
                OrderStatus::ORDER_COMPLETE->value
            ])
            ->search($filters)
            ->latest()
            ->paginate($request->get('itemsPerPage', 10));
        return OrderResource::collection($items);
    }

    public function show(Order $order)
    {
        $order->load(['orderProducts.product']);
        return $this->respondSuccessfully(new OrderResource($order));
    }

    public function cancel(Request $request, $id)
    {
        //주문취소의 경우 모든 주문상품상태가 [결제완료, 배송준비중] 상태만 가능
        $order = Order::with('orderProducts')->canOrderCancel()->findOrFail($id);
        if (!$order->canOrderCancel()) {
            $m = '모든 상품이 ' . implode(', ', OrderStatus::getCanOrderCancelValues()) . '에만 주문 취소할 수 있습니다.';//결제완료, 배송준비중
            //abort(403, $m);
            abort(response()->json(['message' => $m, 'errors' => ['order' => $m]], 403));
        }

        $order = DB::transaction(function () use ($order) {
            if (config('iamport.payment_integration')) {
                $accessToken = Iamport::getAccessToken();
                $result = Iamport::cancel($accessToken, $order->imp_uid);
                if (!$result['response']) abort(403, $result['message']);
            }
            $order->cancel();
            return $order;
        });
        return $this->respondSuccessfully(OrderResource::make($order));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate(['status' => ['required', 'in:' . implode(',', OrderStatus::values())]]);
        $order->update($data);
        $order->syncStatusOrderProducts();
        return $this->respondSuccessfully();
    }
}
