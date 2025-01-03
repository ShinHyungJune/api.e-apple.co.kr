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
            if (!($request->guest_id)) {
                //abort(403, '비회원 아이디가 없습니다.');
                abort(response()->json(['message' => '비회원 아이디가 없습니다.',
                    'errors' => ['guest_id' => '비회원 아이디가 없습니다.']],
                    403));
            }
            $query->where('guest_id', $request->guest_id);
        }
    }

    public function scopePossibleExchangeReturnStatus(Builder $query)
    {
        //if (config('env.app' === 'local')) return; //FORTEST
        $query->where('status', OrderStatus::DELIVERY);//배송중인 경우
    }

    public function exchangeReturns(): HasMany
    {
        return $this->hasMany(ExchangeReturn::class);
    }


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function syncStatusOrder()
    {
        if ($this->order->orderProducts->every(fn($e) => $e->status === OrderStatus::PURCHASE_CONFIRM)) {
            $this->order->update(['status' => OrderStatus::PURCHASE_CONFIRM]);
        }
    }

    public function scopeDelivery(Builder $query)
    {
        //if (config('env.app' === 'local')) return; //FORTEST
        $query->where('status', OrderStatus::DELIVERY);//배송완료인 경우
    }

    public function getDepositPoints()
    {
        if (auth()->check()) {
            $orderProductPaymentAmount = $this->price * $this->quantity;
            $orderProductsPaymentAmountSum = $this->order->orderProducts->sum(fn($orderProduct) => $orderProduct['price'] * $orderProduct['quantity']);
            $orderPaymentAmount = $this->order->price;

            $paymentAmount = ($orderProductPaymentAmount / $orderProductsPaymentAmountSum) * $orderPaymentAmount;

            $orderPointsRate = auth()->user()->level->purchaseRewardPointsRate();//주문 적립율
            $amount = ($orderPointsRate / 100) * $paymentAmount;//최종결제액
            return [$amount, '주문적립'];
        }
        return null;
    }

}
