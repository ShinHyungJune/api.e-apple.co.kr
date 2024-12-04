<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductInquiryResource extends JsonResource
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
            ...$this->only('id', 'is_visible', 'answered_at'),
            'is_answered' => $this->answered_at ? true : false,
            //'product' => ProductResource::make($this->product),
        ];

        if ($this->is_visible) {
            $return = [...$return, ...$this->only('title', 'content', 'answer')];
        }

        //*
        if (config('scribe.response_file')) {
            $comments = ['is_answered' => '답변여부', 'answered_at' => '답변일시'];
            return getScribeResponseFile($return, 'product_inquiries', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
