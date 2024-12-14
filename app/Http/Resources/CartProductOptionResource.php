<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductOptionResource extends JsonResource
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
            ...$this->only(['id', 'price', 'quantity']),
            'name' => $this->productOption?->name ?? null,
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [
            ];
            return getScribeResponseFile($return, 'cart_product_options', $comments);
        }
        //*/
        return $return;
        //*/

    }
}
