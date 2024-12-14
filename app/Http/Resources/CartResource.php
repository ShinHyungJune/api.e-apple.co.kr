<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            ...$this->only(['id']),
            'product' => ProductResource::make($this->whenLoaded('product')),
            'cart_product_options' => CartProductOptionResource::collection($this->cartProductOptions)
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [
            ];
            return getScribeResponseFile($return, 'carts', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
