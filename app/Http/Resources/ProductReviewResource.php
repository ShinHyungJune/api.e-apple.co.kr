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
        $return = [
            ...$this->only(['rating', 'review']),
            'user' => UserResource::make($this->user, true),
            'images' => $this->getMedia(ProductReview::IMAGES) ? ProductReviewImageResource::collection($this->getMedia(ProductReview::IMAGES)) : null,
            'created_date' => $this->created_at->format('Y.m.d')
        ];
        //*
        return $return;
        /*/
        $comments = ['created_date' => '생성일'];
        return getScribeResponseFile($return, 'product_reviews', $comments);
        //*/
    }

}
