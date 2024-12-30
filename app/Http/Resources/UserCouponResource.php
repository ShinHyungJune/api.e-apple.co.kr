<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);

        $return = [
            ...$this->only(['id', 'name', 'type', 'discount_amount', 'minimum_purchase_amount', 'discount_rate', 'usage_limit_amount', 'valid_days', 'issued_until']),
            'user_coupon_id' => $this->pivot->id,
            //'expired_at' => $this->pivot->expired_at,
            'expiration_left_days' => (int)now()->diffInDays($this->pivot->expired_at)
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [
                'user_coupon_id' => '사용자가 발급받은 쿠폰아이디',
                'expiration_left_days' => '쿠폰만료까지 남은 일수'
            ];
            return getScribeResponseFile($return, 'coupons', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
