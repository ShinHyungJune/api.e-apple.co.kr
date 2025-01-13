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
        $additionalFields = ($request->user()?->is_admin) ? [
            'orderProduct' => OrderProductResource::make($this->whenLoaded('orderProduct')),
            'type_label' => $this->type_label,
            'status' => $this->status,
            'status_label' => ExchangeReturnStatus::from($this->status)->label(),
            'refund_bank_name' => $this->refund_bank_name,
            'refund_bank_owner' => $this->refund_bank_owner,
            'refund_bank_account' => $this->refund_bank_account,
            'refund_reason' => $this->refund_reason,
            'refund_amount' => $this->refund_amount,
            'refund_delivery_fee' => $this->refund_delivery_fee
        ] : [
            'status' => ExchangeReturnStatus::from($this->status)->label(),
        ];
        $return = [
            ...$additionalFields,
            ...$this->only(['id', 'type', 'problem', 'description'
                /*'change_of_mind', 'delivery_issue', 'product_issue',*/]),
            'order' => OrderResource::make($this->whenLoaded('order')),
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
