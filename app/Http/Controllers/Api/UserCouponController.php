<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\Request;

/**
 * @group Coupon(쿠폰)
 */
class UserCouponController extends Controller
{

    /**
     * 사용 가능한 사용자 쿠폰 목록
     * @priority 1
     * @queryParam total_order_amount 총주문금액(상품가격) Example: 1000
     * @responseFile storage/responses/coupons.json
     */
    public function index(Request $request)
    {
        $totalOrderAmount = (int)$request->input('total_order_amount');
        $items = Coupon::query()
            ->whereHas('users', function ($query) {
                $query->where('user_id', auth()->id()) //현재 로그인한 사용자와 연결된 쿠폰만 필터링
                ->where('expired_at', '>=', now()) //만료되지 않은 쿠폰
                ->whereNull('used_at') //사용하지 않은 쿠폰
                ;
            })
            ->where(function ($query) use ($totalOrderAmount) {
                $query->where('type', Coupon::TYPE_RATE)
                    ->orWhere(function ($query) use ($totalOrderAmount) {
                        $query->when('amount', function ($query) use ($totalOrderAmount) {
                            //타입이 amount 인 경우는 최소 결제액 확인
                            $query->where('type', Coupon::TYPE_AMOUNT)->where('minimum_purchase_amount', '>=', $totalOrderAmount);
                        });
                    });
            })
            ->latest()->paginate($request->get('take', 10));
        return CouponResource::collection($items);
    }
}
