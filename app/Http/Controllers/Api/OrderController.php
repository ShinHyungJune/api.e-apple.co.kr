<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Requests\CartOrderRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\CartProductOption;
use App\Models\Iamport;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @group Order(주문)
 */
class OrderController extends ApiController
{

    /**
     * 목록
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/orders.json
     */
    public function index(Request $request)
    {
        $items = Order::with(['orderProducts.product'])->mine($request)
            ->afterPending()
            ->latest()->paginate($request->get('take', 10));
        return OrderResource::collection($items);
    }

    /**
     * @deprecated orderProducts 별로 구매확정
     * 구매확정
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/order.json
     */
    public function confirm(Request $request, $id)
    {
        $order = Order::mine($request)
            ->delivery()
            ->findOrFail($id);
        $order = DB::transaction(function () use ($order) {
            $order->update(['status' => OrderStatus::PURCHASE_CONFIRM->value, 'purchase_confirmed_at' => now()]);
            $order->syncStatusOrderProducts();

            //적립금
            if (auth()->check()) auth()->user()->depositPoint($order);

            return $order;
        });

        return $this->respondSuccessfully(OrderResource::make($order));
    }

    /**
     * 생성(장바구니 상품구매, 상품확인): 장바구니 ids
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/order.json
     */
    public function cartsStore(CartOrderRequest $request)
    {
        $data = $request->validated();
        $data['status'] = OrderStatus::ORDER_PENDING->value;
        $cartProductOptions = CartProductOption::with(['productOption'])->mine($request)->whereIn('cart_id', $data['cart_ids'])->get();

        $data = [
            ...$data,
            'total_amount' => $cartProductOptions->sum(function ($cartProductOption) {
                return $cartProductOption->price * $cartProductOption->quantity;
            }),
            'delivery_fee' => $cartProductOptions->pluck('productOption.product.delivery_fee')->filter()->max(),
            'order_products' => $cartProductOptions->map(function ($cartProductOption) {
                return [
                    'product_id' => $cartProductOption->productOption->product_id,
                    'product_option_id' => $cartProductOption->productOption->id,
                    'quantity' => $cartProductOption->quantity,
                    'price' => $cartProductOption->productOption->price,
                ];
            })->toArray(),
        ];

        Order::checkOrderProducts($data);

        $order = DB::transaction(function () use ($data) {
            $order = tap(new Order($data))->save();
            $order->orderProducts()->createMany($data['order_products']);
            return $order;
        });

        return $this->respondSuccessfully(OrderResource::make($order));
    }

    /**
     * 생성(바로구매, 상품확인): 주문 상품, 상품옵션, 수량
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/order.json
     */
    public function store(OrderRequest $request)
    {
        $data = $request->validated();
        Order::checkOrderProducts($data);

        $order = DB::transaction(function () use ($data) {
            $order = tap(new Order($data))->save();
            $order->orderProducts()->createMany($data['order_products']);
            return $order;
        });

        return $this->respondSuccessfully(OrderResource::make($order));
    }

    /**
     * 수정(결제시도): 주문자 정보, 배송지 정보, 주문금액, 쿠폰, 적립금 사용액, 배송비, 결제수단 등
     * @unauthenticated
     * @responseFile storage/responses/order.json
     */
    public function update(OrderRequest $request, $id)
    {
        $order = Order::with('orderProducts.productOption')->mine($request)->pending()->findOrFail($id);

        $data = $request->validated();
        $coupon = (!auth()->check()) ? null : auth()->user()->availableCoupons()->wherePivot('id', $data['user_coupon_id'])->first();
        $order->checkOrderAmount($data, $coupon);

        $order->complete($data, $coupon);

        return $this->respondSuccessfully(OrderResource::make($order));
    }

    /**
     * 결제검증(OrderObserver 사용)
     * @unauthenticated
     */
    public function paymentComplete(Request $request)
    {
        $request->validate([
            'imp_uid' => ['required', 'string', 'max:50000'],
            'merchant_uid' => ['required', 'string', 'max:50000']
        ]);

        $path = $request->path();

        // 결제를 성공하면 pg사쪽에서 웹훅이란걸 발송해주는데 가끔 너무 빨리 줘서 결제성공처리가 중복으로 될 때가 있음. 이름 방지하기 위해 3초 대기시킴
        if (strpos($path, 'webhook') !== false)
            sleep(3); // 3초 대기 (중복방지)

        //결제내역 확인
        if (!Iamport::PAYMENT_INTEGRATION) { //FORTEST
            $impOrder = Order::selectRaw("*, payment_amount AS amount")->where("merchant_uid", $request->merchant_uid)->first()->toArray();
            $impOrder['status'] = 'paid';//paid, ready
        } else {
            $accessToken = Iamport::getAccessToken(); // 권한 얻기
            $impOrder = Iamport::getOrder($accessToken, $request->imp_uid); // 주문조회
        }
        if (empty($impOrder)) {
            abort(404, '결제 내역이 없습니다.');
        }


        //주문내역 확인
        $order = Order::with('orderProducts')->where(function ($query) {
            if (Iamport::PAYMENT_INTEGRATION) {
                $query->where("status", OrderStatus::ORDER_COMPLETE)->orWhere("status", OrderStatus::PAYMENT_PENDING);
            }
        })->where("merchant_uid", $impOrder["merchant_uid"])->first();
        if (!$order) {
            abort(404, '주문 내역이 없습니다.');
        }


        //결제금액 확인
        if ($order->payment_amount != $impOrder["amount"]) {
            $message = '결제금액 오류 => ' . $order->payment_amount . ':' . $impOrder["amount"];
            $order->update(['payment_fail_reason' => $message, 'status' => OrderStatus::PAYMENT_FAIL]);
            $order->syncStatusOrderProducts();
            abort(403, $message);
        }

        try {
            DB::transaction(function () use ($request, $order, $impOrder) {
                //throw new \Exception('TEST 오류 발생');

                switch ($impOrder["status"]) {
                    case "ready": // 가상계좌 발급
                        $vbankDate = Carbon::parse($impOrder["vbank_date"])->format("Y-m-d H:i");
                        // OrderObserver 사용
                        $order->update(["imp_uid" => $request->imp_uid, "status" => OrderStatus::PAYMENT_PENDING,
                            "vbank_num" => $impOrder["vbank_num"], "vbank_date" => $vbankDate, "vbank_name" => $impOrder["vbank_name"]]);
                        $order->syncStatusOrderProducts();
                        break;
                    case "paid": // 결제완료
                        // OrderObserver 사용
                        $order->update(["imp_uid" => $request->imp_uid, "status" => OrderStatus::PAYMENT_COMPLETE, "payment_completed_at" => now()]);
                        $order->syncStatusOrderProducts();
                        break;
                }
            });
        } catch (\Exception $e) {
            $order->update(['payment_fail_reason' => $e->getMessage(), 'status' => OrderStatus::PAYMENT_FAIL]);
            $order->syncStatusOrderProducts();
            abort(500, $e->getMessage());
        }

        return $this->respondSuccessfully(OrderResource::make($order));
    }

    /**
     * 주문취소(사용자)
     * @responseFile storage/responses/order.json
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::mine($request)->deliveryBefore()->findOrFail($id);

        $order = DB::transaction(function () use ($order) {
            if (Iamport::PAYMENT_INTEGRATION) {
                $accessToken = Iamport::getAccessToken();
                $result = Iamport::cancel($accessToken, $this->imp_uid);
                if (!$result['response']) abort(403, $result['message']);
            }

            $order->cancel();

            return $order;
        });

        return $this->respondSuccessfully(OrderResource::make($order));
    }


    /**
     * 주문상세
     * @unauthenticated
     * @responseFile storage/responses/order.json
     */
    public function show(Request $request, $id)
    {
        /*if (\App\Http\Controllers\Api\auth()->user() && $order->user_id != \App\Http\Controllers\Api\auth()->id())
            return $this->respondForbidden();

        if (!\App\Http\Controllers\Api\auth()->user() && $order->guest_id != $request->guest_id)
            return $this->respondForbidden();*/
        $order = Order::with(['orderProducts'])->mine($request)->findOrFail($id);

        return $this->respondSuccessfully(OrderResource::make($order));
    }


    /**
     * 비회원 주문조회
     * @unauthenticated
     * @responseFile storage/responses/order.json
     */
    public function showGuest(Request $request)
    {
        $request->validate([
            'buyer_name' => ['required', 'string', 'max:255'],
            'merchant_uid' => ['required', 'string', 'max:255'],
        ]);

        $order = Order::with(['orderProducts'])
            ->whereNull('user_id')
            ->where('buyer_name', $request->buyer_name)->where('merchant_uid', $request->merchant_uid)
            ->first();
        if (empty($order)) {
            abort(404, '주문내역이 없습니다.');
        }

        return $this->respondSuccessfully(OrderResource::make($order));
    }

}
