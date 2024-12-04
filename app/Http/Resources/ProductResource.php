<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ProductResource extends JsonResource
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
            //...parent::toArray($request);
            ...$this->only([
                'id', 'name', 'view_count',
                'price',
                'original_price',
                'delivery_fee',
                'shipping_origin',
                'fruit_size',
                'sugar_content',
                'food_type',
                'manufacturer_and_location',
                'importer',
                'origin',
                'ingredients_and_composition',
                'storage_and_handling',
                'manufacture_date',
                'expiration_date',
                'gmo_desc',
                'customer_service_contact'
            ]),

            'is_new' => Carbon::parse($this->created_at)->greaterThanOrEqualTo(Carbon::now()->subDay()),
            'average_rating' => $this->reviews->avg('rating'),
            'reviews_count' => $this->reviews->count(),
            'inquiries_count' => $this->whenLoaded('inquiries') ? $this->inquiries->count(): $this->inquiries_count,

            'categories' => $this->categories ? ProductCategoryResource::collection($this->categories) : null,
            'product_images' => $this->getMedia(Product::IMAGES) ? ProductImageResource::collection($this->getMedia(Product::IMAGES)) : null,
            'product_desc_images' => $this->getMedia(Product::DESC_IMAGES) ? ProductDescImageResource::collection($this->getMedia(Product::DESC_IMAGES)) : null,
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [
                'id' => '기본키',
                'is_new' => '새로운상품 여부',
                'average_rating' => '평균 평점',
            ];
            return getScribeResponseFile($return, 'products', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
