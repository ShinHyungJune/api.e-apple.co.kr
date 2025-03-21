<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        $additionalFields = ($request->user()?->is_admin) ? [
            'order_id' => $this->order_id,
            'can_delivery' => $this->status === OrderStatus::DELIVERY_PREPARING,
            'delivery_company' => $this->delivery_company,
            'delivery_tracking_number' => $this->delivery_tracking_number
        ] : [];
        $return = [
            ...$additionalFields,
            ...$this->only(['id', 'quantity', 'price', 'updated_at', 'delivery_tracking_number']),
            'status' => OrderStatus::from($this->status->value)->label() ?? null,
            'product' => ProductResource::make($this->whenLoaded('product')),
            'productOption' => ProductOptionResource::make($this->productOption),
            'exchangeReturns' => OrderProductResource::collection($this->whenLoaded('exchangeReturns')),
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [ 'created_at'=>'등록일자', 'updated_at'=>'수정일자' ];
            return getScribeResponseFile($return, 'order_products', $comments);
        }
        //*/
        return $return;
        //*/

    }
}
