<?php

namespace App\Http\Resources;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InquiryResource extends JsonResource
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
            'user' => UserResource::make($this->whenLoaded('user')),
        ] : [];
        $return = [
            ...$additionalFields,
            ...$this->only(['id',
                /*'purchase_related_inquiry', 'general_consultation_inquiry',*/
                'type', 'content', 'created_at', 'answer', 'answered_at', 'user_id']),
            'is_answer' => !is_null($this->answered_at),
            'img' => $this->getMedia(Inquiry::IMAGES) ? MediaResource::make($this->getMedia(Inquiry::IMAGES)[0] ?? null) : null,
            'imgs' => $this->getMedia(Inquiry::IMAGES) ? MediaResource::collection($this->getMedia(Inquiry::IMAGES)) : null,
        ];
        //*
        if (config('scribe.response_file')) {
            $comments = [
                'is_answer' => '답변여부'
                //'created_date' => '생성일'
            ];
            return getScribeResponseFile($return, 'inquiries', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
