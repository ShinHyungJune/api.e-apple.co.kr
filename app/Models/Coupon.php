<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    const TYPE_AMOUNT = 'amount';
    const TYPE_RATE = 'rate';

    const TYPES = [self::TYPE_AMOUNT, self::TYPE_RATE];

    protected $guarded = ['id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_coupons')
            ->withTimestamps()
            ->withPivot('used_at'); // 중간 테이블의 추가 필드를 사용하려면
    }

    public function mineAvailables()
    {
        return $this->belongsToMany(User::class, 'user_coupons')
            ->withTimestamps()
            // 중간 테이블의 추가 필드를 사용하려면
                ->withPivot('used_at')
            // 중간 테이블 검색
                ->wherePivot('user_id', auth()->id()) //현재 로그인한 사용자와 연결된 쿠폰만 필터링
                ->wherePivot('expired_at', '>=', now()) //만료되지 않은 쿠폰
                ->wherePivot('used_at', null); //사용하지 않은 쿠폰

    }

    public function getDiscountAmountByType($totalAmount)
    {
        $result = 0;
        if ($this->type === self::TYPE_AMOUNT) {
            if ($totalAmount >= $this->minimum_purchase_amount) {//결제액은 최소결제액 이상
                $result = $this->discount_amount;
            }
        }

        if ($this->type === self::TYPE_RATE) {
            $result = $totalAmount * ($this->discount_rate / 100);
            if ($result > $this->usage_limit_amount) {
                $result = $this->usage_limit_amount;//사용한도금액
            }
        }

        return (int)round($result);
    }

}
