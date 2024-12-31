<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            ...$this->only([
                'id', 'buyer_name', 'buyer_email', 'buyer_contact', 'buyer_address_zipcode', 'buyer_address', 'buyer_address_detail',
                'delivery_name', 'delivery_phone', 'delivery_postal_code', 'delivery_address', 'delivery_address_detail', 'delivery_request', 'common_entrance_method',
                'total_amount', 'user_coupon_id', 'coupon_discount', 'use_points', 'delivery_fee', 'price',
                'imp_uid', 'merchant_uid',
                'pay_method_pg', 'pay_method_method',
                'created_at', 'updated_at', 'delivery_started_at', 'purchase_confirmed_at',
                'delivery_tracking_number',
                'refund_amount', 'refund_delivery_fee'
            ]),
            //'status' => OrderStatus::from($this->status)->label(),
            'status' => OrderStatus::from($this->status->value)->label(),
            'orderProducts' => OrderProductResource::collection($this->whenLoaded('orderProducts')),
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [ 'created_at'=>'등록일자', 'updated_at'=>'수정일자' ];
            return getScribeResponseFile($return, 'orders', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
