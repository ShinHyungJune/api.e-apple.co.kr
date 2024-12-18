<?php

namespace App\Http\Resources;

use App\Enums\UserLevel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            ...$this->only(['id', 'name', 'email', 'phone', 'nickname', 'points', 'available_coupons_count']),
            'level' => UserLevel::from($this->level->value)->label(),
        ];

        //*/
        if (config('scribe.response_file')) {
            $comments = ['available_coupons_count'=>'사용 가능 쿠폰 개수'];
            return getScribeResponseFile($return, 'users', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
