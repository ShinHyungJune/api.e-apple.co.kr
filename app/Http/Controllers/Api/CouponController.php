<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\Request;

/**
 * @group Coupon(쿠폰)
 */
class CouponController extends ApiController
{

    /**
     * 다운로드 가능한 목록
     * @priority 1
     * @responseFile storage/responses/coupons.json
     */
    public function index(Request $request)
    {
        $items = Coupon::query()
            /*->with(['users' => function ($query) {
                $query->where('user_id', auth()->id()); // 현재 로그인한 사용자와 연결된 쿠폰만 필터링
            }])*/
            ->with(['users'])
            ->where('issued_until', '>=', now())
            ->latest()->paginate($request->get('take', 10));
        $items->map(function ($coupon) {
            // 사용자가 해당 쿠폰을 다운받았는지 여부 확인
            $coupon->is_downloaded = $coupon->users->contains('id', auth()->id());
            return $coupon;
        });

        return CouponResource::collection($items);
    }

    /**
     * 쿠폰 다운로드
     * @priority 1
     */
    public function download(Request $request, Coupon $coupon)
    {
        auth()->user()->coupons()->attach($coupon, ['issued_at' => now(), 'expired_at' => now()->addDay($coupon->valid_days)]);
        return $this->respondSuccessfully();
    }



}
