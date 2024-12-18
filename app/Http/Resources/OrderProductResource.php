<?php

namespace App\Http\Resources;

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
            ...$this->only(['quantity', 'price']),
            'product' => ProductResource::make($this->whenLoaded('product')),
            'productOption' => ProductOptionResource::make($this->productOption),
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [
            ];
            return getScribeResponseFile($return, 'order_products', $comments);
        }
        //*/
        return $return;
        //*/

    }
}
