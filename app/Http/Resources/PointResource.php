<?php

namespace App\Http\Resources;

use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointResource extends JsonResource
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
            ...$this->only(['id', 'pointable_type', 'pointable_id', 'deposit', 'withdrawal', 'balance', 'description', 'created_at', 'expired_at']),
            'expiration_date' => $this->created_at->addDays(Point::EXPIRATION_DAYS)->format('Y-m-d'),
            'order_id' => $this->orderId,
        ];

        //*
        if (config('scribe.response_file')) {
            $comments = [
                'expiration_date' => '소멸예정일'
            ];
            return getScribeResponseFile($return, 'points', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
