<?php

namespace App\Http\Resources;

use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
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
            'created_at' => $this->created_at
        ] : [];
        $return = [
            ...$additionalFields,
            ...$this->only(['id', 'rating', 'review']),
            'user' => UserResource::make($this->whenLoaded('user'), !$request->user()?->is_admin),
            'img' => $this->getMedia(ProductReview::IMAGES) ? MediaResource::make($this->getMedia(ProductReview::IMAGES)[0] ?? null) : null,
            'imgs' => $this->getMedia(ProductReview::IMAGES) ? MediaResource::collection($this->getMedia(ProductReview::IMAGES)) : null,
            'created_date' => $this->created_at?->format('Y.m.d'),
            'product' => ProductResource::make($this->whenLoaded('product')),
            'productOption' => ProductOptionResource::make($this->whenLoaded('productOption')),
            'orderProduct' => OrderProductResource::make($this->whenLoaded('orderProduct')),
        ];
        //*
        if (config('scribe.response_file')) {
            $comments = ['created_date' => '생성일'];
            return getScribeResponseFile($return, 'product_reviews', $comments);
        }
        //*/
        return $return;
        //*/
    }

}
