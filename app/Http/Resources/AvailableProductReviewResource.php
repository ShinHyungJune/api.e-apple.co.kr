<?php

namespace App\Http\Resources;

use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableProductReviewResource extends JsonResource
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
            ...$this->only(['id', 'order_id', 'quantity', 'price']),
            'd_day' => abs(floor(ProductReview::AVAILABLE_DAYS - $this->created_at->diffInDays(now()))),
            'product' => ProductResource::make($this->whenLoaded('product')),
            'productOption' => ProductOptionResource::make($this->whenLoaded('productOption')),
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [
                'd_day' => '리뷰작성 가능 일자'
            ];
            return getScribeResponseFile($return, 'order_products', $comments);
        }
        //*/
        return $return;
        //*/

    }
}
