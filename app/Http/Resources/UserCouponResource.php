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
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [
                'user_coupon_id' => '사용자가 발급받은 쿠폰아이디'
            ];
            return getScribeResponseFile($return, 'coupons', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
