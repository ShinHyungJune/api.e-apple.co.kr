<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderProduct extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => OrderStatus::class, // Enum 캐스팅
    ];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function productOption(): HasOne
    {
        return $this->hasOne(ProductOption::class, 'id', 'product_option_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(ProductReview::class);
    }

    public function scopeMine(Builder $query, $request)
    {
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            if (!($request->guest_id)) abort(403, '비회원 아이디가 없습니다.');
            $query->where('guest_id', $request->guest_id);
        }
    }

    public function scopePossibleExchangeReturnStatus(Builder $query)
    {
        //if (config('env.app' === 'local')) return; //FORTEST
        $query->where('status', OrderStatus::DELIVERY);//배송완료인 경우
    }

    public function exchangeReturns(): HasMany
    {
        return $this->hasMany(ExchangeReturn::class);
    }


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }


}
