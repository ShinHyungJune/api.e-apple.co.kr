<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    const MIN_PAYMENT_AMOUNT = 1000;//최소결제금액

    protected $guarded = ['id'];

    protected $casts = [
        'status' => OrderStatus::class, // Enum 캐스팅
    ];

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public static function checkOrderProducts($data)
    {
        $orderProducts = (!empty($data['order_products'])) ? collect($data['order_products']) : $data['orderProducts'];
        $productOptionIds = $orderProducts->pluck('product_option_id');
        $productOptions = ProductOption::with('product')->whereIn('id', $productOptionIds)->get();

        /**
         * 상품가격(total_amount) 및 상품재고 확인
         */
        $totalAmount = 0;
        foreach ($orderProducts as $e) {
            $productOption = $productOptions->where('product_id', $e['product_id'])->where('id', $e['product_option_id'])->first();
            if ($e['quantity'] > $productOption['stock_quantity']) {
                abort(422, '상품재고를 확인해주세요.');
            }
            $totalAmount += $productOption['price'] * $e['quantity'];
        }
        /**
         * 상품가격 확인 total_amount
         */
        /*$totalAmount = $orderProducts->sum(function ($e) use ($productOptions) {
            $productOption = $productOptions->where('product_id', $e['product_id'])->findOrFail($e['product_option_id']);
            return $e['quantity'] * $productOption->price;
        });*/
        if ($data['total_amount'] !== $totalAmount) {
            abort(422, '상품금액을 확인해주세요.');
        }

        /**
         * 배송비 확인
         */
        if ($data['delivery_fee'] > 0) {
            $deliveryFee = $productOptions->pluck('product.delivery_fee')->filter()->max();
            if ($data['delivery_fee'] !== $deliveryFee) abort(422, '배송비를 확인해주세요.');
        }

        return true;
    }

    public function checkOrderAmount($data, $coupon = null)
    {

        self::checkOrderProducts($this->only('total_amount', 'orderProducts', 'delivery_fee'));

        /**
         * 쿠폰 확인
         */
        $couponDiscountAmount = 0;
        if ($data['user_coupon_id'] > 0 && $data['coupon_discount_amount'] > 0) {
            if ($data['user_coupon_id'] !== $coupon?->pivot->id) {
                abort(422, '쿠폰을 확인해주세요.');
            }
            $couponDiscountAmount = $coupon?->getDiscountAmountByType($this->total_amount) ?? 0;
            if ($data['coupon_discount_amount'] !== $couponDiscountAmount) {
                abort(422, '쿠폰 할인금액을 확인해주세요.');
            }
        }


        /**
         * 적립금 확인
         */
        $usePoint = 0;
        if ($data['use_points'] > 0) {
            if ($data['use_points'] > auth()->user()->points) {
                abort(422, '적립금 사용액을 확인해주세요.');
            }
            $usePoint = $data['use_points'];
        }


        /**
         * 최종결제금액 확인
         */
        $paymentAmount = $this->total_amount - $couponDiscountAmount - $usePoint + $this->delivery_fee;
        if ($data['payment_amount'] !== $paymentAmount) {
            abort(422, '최종 결제금액을 확인해주세요.');
        }
        if ($data['payment_amount'] < Order::MIN_PAYMENT_AMOUNT) {
            abort(422, '최소결제금액은 ' . number_format(Order::MIN_PAYMENT_AMOUNT) . '원 입니다.');
        }

        return true;
    }

    public function scopeMine(Builder $query, $request)
    {
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            if (!($request->guest_id > 0)) abort(403, '비회원 아이디가 없습니다.');
            $query->where('guest_id', $request->guest_id);
        }
    }


    public function scopePending(Builder $query)
    {
        $query->where('status', OrderStatus::ORDER_PENDING);//
    }

    public function scopeDelivery(Builder $query)
    {
        $query->where('status', OrderStatus::DELIVERY);//배송완료인 경우
    }

    /**
     * @deprecated
     */
    public function scopeDeliveryComplete(Builder $query)
    {
        $query->where('status', OrderStatus::DELIVERY_COMPLETE);//배송완료인 경우
    }

    public function exchangeReturns(): HasMany
    {
        return $this->hasMany(ExchangeReturn::class);
    }

    public function getDepositPoints()
    {
        if (auth()->check())
        {
            $order_points_rate = auth()->user()->level->purchaseRewardPointsRate();//주문 적립율
            $amount = $order_points_rate * $this->payment_amount;//최종결제액
            $desc = '주문적립';
            return [$amount, $desc];
        }
        return null;
    }

    public function getWithdrawalPoints()
    {
        return [$this->use_points, '주문사용'];
    }

}
