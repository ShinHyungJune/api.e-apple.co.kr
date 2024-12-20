<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
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
        $items = Order::with(['orderProducts'])->mine($request)->latest()->paginate($request->get('take', 10));
        return OrderResource::collection($items);
    }

    /**
     * 주문확정
     * @priority 1
     * @unauthenticated
     * @responseFile storage/responses/order.json
     */
    public function confirm(Request $request, $id)
    {
        $order = Order::mine($request)->delivery()->findOrFail($id);
        $order = DB::transaction(function () use ($order) {
            $order->update(['status' => OrderStatus::PURCHASE_CONFIRM->value, 'purchase_confirmed_at' => now()]);

            //적립금
            if (auth()->check()) auth()->user()->depositPoint($order);

            return $order;
        });

        return $this->respondSuccessfully(OrderResource::make($order));
    }

    /**
     * 생성(상품확인): 주문 상품, 상품옵션, 수량
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
    public function complete(Request $request)
    {
        $request->validate([
            'imp_uid' => ['required', 'string', 'max:50000'],
            'merchant_uid' => ['required', 'string', 'max:50000']
        ]);

        $path = $request->path();

        // 결제를 성공하면 pg사쪽에서 웹훅이란걸 발송해주는데 가끔 너무 빨리 줘서 결제성공처리가 중복으로 될 때가 있음. 이름 방지하기 위해 3초 대기시킴
        if (strpos($path, 'webhook') !== false)
            sleep(3); // 3초 대기 (중복방지)

        if (!Iamport::PAYMENT_INTEGRATION) { //FORTEST
            $impOrder = Order::selectRaw("*, payment_amount AS amount")->where("merchant_uid", $request->merchant_uid)->first();
        } else {
            $accessToken = Iamport::getAccessToken(); // 권한 얻기
            $impOrder = Iamport::getOrder($accessToken, $request->imp_uid); // 주문조회
        }

        $order = Order::where(function ($query) {
            if (Iamport::PAYMENT_INTEGRATION) {
                $query->where("status", OrderStatus::ORDER_COMPLETE)->orWhere("status", OrderStatus::PAYMENT_PENDING);
            }
        })->where("merchant_uid", $impOrder["merchant_uid"])->first();


        DB::beginTransaction();
        try {
            if (!$order) abort(404);
            if ($order->payment_amount != $impOrder["amount"]) abort(403);

            switch ($impOrder["status"]) {
                case "ready": // 가상계좌 발급
                    $vbankNum = $impOrder["vbank_num"];
                    $vbankDate = Carbon::parse($impOrder["vbank_date"])->format("Y-m-d H:i");
                    $vbankName = $impOrder["vbank_name"];

                    // OrderObserver 사용
                    $order->update([
                        "imp_uid" => $request->imp_uid,
                        "status" => OrderStatus::PAYMENT_PENDING,
                        "vbank_num" => $vbankNum,
                        "vbank_date" => $vbankDate,
                        "vbank_name" => $vbankName
                    ]);

                    $result = ["success" => 1, "message" => "가상계좌 발급이 완료되었습니다."];

                    break;
                case "paid": // 결제완료
                    // OrderObserver 사용
                    $order->update(["imp_uid" => $request->imp_uid, "status" => OrderStatus::PAYMENT_COMPLETE]);
                    $result = ["success" => 1, "message" => "결제가 완료되었습니다."];
                    break;
            }
            DB::commit();
        } catch (\Exception $e) {
            // Iamport::cancel($accessToken, $request->imp_uid);
            $order->update(['status' => $e->getMessage()]);
            // $order->update(["state" => StateOrder::BEFORE_PAYMENT]);
            $result = ["success" => 0, "message" => "결제를 실패하였습니다."];
            DB::rollBack();
        }

        /*$order = Order::where("merchant_uid", $request->merchant_uid)->first();
        // 모바일 결제 redirect가 필요할 경우
        if (strpos($request->path(), "mobile"))
            return \App\Http\Controllers\Api\redirect(config("app.client_url") . "/orders/result?merchant_uid={$order->merchant_uid}&buyer_contact={$order->buyer_contact}&buyer_name={$order->buyer_name}");*/

        return $this->respondSuccessfully(OrderResource::make($order));
    }

    /** 취소
     * @group Order(주문)
     * @responseFile storage/responses/order.json
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::mine($request)->/*deliveryBefore()->*/ findOrFail($id);

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


    /** 회원용 상세
     * @group Order(주문)
     * @responseFile storage/responses/order.json
     */
    public function show(Order $order, Request $request)
    {
        /*if (\App\Http\Controllers\Api\auth()->user() && $order->user_id != \App\Http\Controllers\Api\auth()->id())
            return $this->respondForbidden();

        if (!\App\Http\Controllers\Api\auth()->user() && $order->guest_id != $request->guest_id)
            return $this->respondForbidden();*/

        return $this->respondSuccessfully(OrderResource::make($order));
    }

}
