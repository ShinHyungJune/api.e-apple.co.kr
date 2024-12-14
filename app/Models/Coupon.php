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

}
