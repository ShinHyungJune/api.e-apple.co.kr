<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCouponCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return $this->collection->map(function ($item) use ($request) {
            //return ($item)->toArray($request);
            return [
                'id' => $item->id,
                'issued_at' => $item->issued_at,
                'expired_at' => $item->expired_at,
                'order_id' => $item->order_id,
                'used_at' => $item->used_at,
                'user' => new UserResource($item->user),
                'coupon' => new CouponResource($item->coupon),
            ];
        })->all();
    }
}
