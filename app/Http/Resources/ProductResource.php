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
            //'reviews_count' => $this->reviews_count,
            'average_rating' => $this->reviews()->avg('rating'),

            'categories' => $this->categories ? ProductCategoryResource::collection($this->categories) : null,
            'product_images' => $this->getMedia(Product::IMAGES) ? ProductImageResource::collection($this->getMedia(Product::IMAGES)) : null,
            'product_desc_images' => $this->getMedia(Product::DESC_IMAGES) ? ProductDescImageResource::collection($this->getMedia(Product::DESC_IMAGES)) : null,
        ];

        //*
        return $return;
        /*/
        $comments = [
            'id' => '기본키',
            'is_new' => '새로운상품 여부',
            'average_rating' => '평균 평점',
            'eye_count' => '????',
        ];
        return getScribeResponseFile($return, 'products', $comments);
        //*/
    }
}
