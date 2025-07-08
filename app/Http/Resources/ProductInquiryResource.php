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
        $additionalFields = ($request->user()?->is_admin) ? [
            'product_id' => $this->product_id,
            'title' => $this->title,
            'content' => $this->content,
            'answer' => $this->answer,
            'is_answered_label' => $this->answered_at ? '답변' : '접수',
            'is_visible_label' => $this->is_visible ? '공개글' : '비밀글',
        ] : [];

        $return = [
            ...$additionalFields,
            'user' => UserResource::make($this->whenLoaded('user')),
            ...$this->only('id', 'is_visible', 'created_at', 'answered_at', 'user_id'),
            'is_answered' => $this->answered_at ? true : false,
            'product' => $this->whenLoaded('product', function () {
                return ProductResource::make($this->product);
            })
        ];

        if ($this->is_visible || $this->user_id === auth()->id()) {
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
