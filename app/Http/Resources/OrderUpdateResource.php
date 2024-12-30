<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderUpdateResource extends JsonResource
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
            'order' => OrderResource::make($this->resource),
            'm_redirect_url' => config("app.url") . "/api/orders/complete/mobile",
            'imp_code' => config("iamport.imp_code"), // 가맹점 식별코드
        ];
        //*
        if (config('scribe.response_file')) {
            $comments = [
                'm_redirect_url' => '리다이렉트 URL',
                'imp_code' => '가맹점 식별코드'
            ];
            return getScribeResponseFile($return, 'orders', $comments);
        }
        //*/
        return $return;
        //*/

    }
}
