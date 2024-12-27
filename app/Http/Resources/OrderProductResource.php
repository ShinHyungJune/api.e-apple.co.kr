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
        $return = [
            ...$this->only(['id', 'quantity', 'price', 'updated_at']),
            'status' => OrderStatus::from($this->status->value)->label(),
            'product' => ProductResource::make($this->whenLoaded('product')),
            'productOption' => ProductOptionResource::make($this->productOption),
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
