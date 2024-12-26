<?php

namespace App\Http\Resources;

use App\Enums\ExchangeReturnStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeReturnResource extends JsonResource
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
            ...$this->only(['id', 'type', 'change_of_mind', 'delivery_issue', 'product_issue', 'description']),
            'order' => OrderResource::make($this->whenLoaded('order')),
            'status' => ExchangeReturnStatus::from($this->status)->label(),
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [
            ];
            return getScribeResponseFile($return, 'exchange_returns', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
