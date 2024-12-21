<?php

namespace App\Http\Resources;

use App\Enums\UserLevel;
use App\Models\ProductReview;
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
            ...$this->only(['id', 'name', 'email', 'phone', 'nickname', 'points',
                'available_coupons_count',//사용 가능한 쿠폰 개수
                'available_product_reviews_count',//작성 가능한 상품 리뷰 개수
                'product_reviews_count', //내 상품 리뷰 개수
            ]),
            // 적립 가능한 적립금 합계 = 등록가능한 리뷰수 * 리뷰 1개당 최대 적립금
            'available_deposit_point' => $this->available_product_reviews_count * ProductReview::PHOTO_REVIEW_POINTS,
            'level' => UserLevel::from($this->level->value)->label(),
        ];

        //*/
        if (config('scribe.response_file')) {
            $comments = [
                'available_coupons_count' => '사용 가능한 쿠폰 개수',
                'available_product_reviews_count' => '작성 가능한 상품 리뷰 개수',
                'product_reviews_count' => '내 상품 리뷰 개수',
                'available_deposit_point' => '적립 가능한 적립금 합계 = 등록가능한 리뷰수 * 리뷰 1개당 최대 적립금'
            ];
            return getScribeResponseFile($return, 'users', $comments);
        }
        //*/
        return $return;
        //*/
    }
}
