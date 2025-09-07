<?php

namespace App\Services;

use App\Enums\CouponTypeMoment;
use App\Models\Coupon;
use App\Models\User;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\Log;

class CouponAutoIssueService
{
    /**
     * 특정 시점에 해당하는 쿠폰을 자동으로 발급
     */
    public function issueCouponsForMoment(User $user, CouponTypeMoment $moment)
    {
        try {
            // 해당 시점에 발급되어야 하는 쿠폰 조회
            $coupons = Coupon::where('type_moment', $moment->value)
                ->where('is_use', true)
                ->get();
            
            foreach ($coupons as $coupon) {
                // 이미 발급받은 쿠폰인지 확인
                $exists = UserCoupon::where('user_id', $user->id)
                    ->where('coupon_id', $coupon->id)
                    ->exists();
                
                if (!$exists) {
                    // 쿠폰 발급
                    UserCoupon::create([
                        'user_id' => $user->id,
                        'coupon_id' => $coupon->id,
                        'expired_at' => now()->addDays($coupon->validity_days ?? 30),
                        'used_at' => null
                    ]);
                    
                    Log::info('자동 쿠폰 발급', [
                        'user_id' => $user->id,
                        'coupon_id' => $coupon->id,
                        'moment' => $moment->value
                    ]);
                }
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('자동 쿠폰 발급 실패', [
                'user_id' => $user->id,
                'moment' => $moment->value,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * 회원가입 시 쿠폰 발급
     */
    public function issueForUserCreate(User $user)
    {
        return $this->issueCouponsForMoment($user, CouponTypeMoment::USER_CREATE);
    }
    
    /**
     * 첫 주문 시 쿠폰 발급
     */
    public function issueForFirstOrder(User $user)
    {
        // 이전 주문이 있는지 확인 (첫 주문인지 체크)
        $previousOrderCount = $user->orders()
            ->whereNotIn('status', ['order_pending', 'payment_fail'])
            ->count();
        
        // 첫 주문인 경우에만 쿠폰 발급
        if ($previousOrderCount === 1) {
            return $this->issueCouponsForMoment($user, CouponTypeMoment::ORDER_CREATE_FIRST);
        }
        
        return false;
    }
}