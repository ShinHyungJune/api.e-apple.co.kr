<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryAddressResource extends JsonResource
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
            'created_at' => $this->created_at,
        ] : [];
        $return = [
            ...$additionalFields,
            ...$this->only(['id', 'name', 'recipient_name', 'phone', 'postal_code', 'address', 'address_detail', 'delivery_request', 'is_default'])
        ];
        //*
        if (config('scribe.response_file')) {
            $comments = [];
            return getScribeResponseFile($return, 'delivery_addresses', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
