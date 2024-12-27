<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        $return = $this->only('id', 'name', 'price', 'original_price', 'stock_quantity');
        //*
        if (config('scribe.response_file')) {
            return getScribeResponseFile($return, 'product_options');
        }
        //*/
        return $return;
        //*/
    }
}
