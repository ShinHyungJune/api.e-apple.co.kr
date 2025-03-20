<?php

namespace App\Http\Requests;

use App\Enums\IamportMethod;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\ProductOption;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $return['status'] = ['required', 'in:' . implode(',', OrderStatus::values())]; // 주문상태

        if (auth()->check()) {
            $return['user_id'] = ['required'/*, 'exists:users,id'*/];
        } else {
            $return['guest_id'] = ['required', 'string'];
        }

        if ($this->isMethod('POST')) {
            $return = [
                ...$return,

                // 주문 금액 관련
                'total_amount' => ['required', 'integer', 'min:0'],//주문총액
                'delivery_fee' => ['nullable', 'integer', 'min:0'],//배송비

                'order_products' => ['required', 'array'],
                'order_products.*.user_id' => ['required_with:user_id'],
                'order_products.*.guest_id' => ['required_with:guest_id'],
                'order_products.*.product_id' => ['required', 'integer'/*, 'exists:products,id'*/],
                'order_products.*.product_option_id' => ['required', 'integer',
                    /* 상품 옵션이 있는지 확인
                     function ($attribute, $value, $fail) {
                        $productIndex = explode('.', $attribute)[1]; // order_products.0.product_option_id -> 0 추출
                        $productId = $this->input("order_products.{$productIndex}.product_id");
                        $isValid = DB::table('product_options')->where('id', $value)->where('product_id', $productId)->exists();
                        if (!$isValid) {
                            //$fail("The selected Option ID ({$value}) is invalid for the given Product ID ({$productId}).");
                            $fail("product_option_id(을)를 입력해주세요.");
                        }
                    }*/
                ],
                'order_products.*.quantity' => ['required', 'integer', 'min:1'],
                'order_products.*.price' => ['required', 'integer', 'min:1'],
                'order_products.*.original_price' => ['nullable', 'integer', 'min:1'],
            ];
        }

        if ($this->isMethod('PUT')) {
            $return = [
                ...$return,
                'buyer_name' => ['required', 'string', 'max:255'], // 주문자명
                'buyer_contact' => ['required', 'string', 'max:20'], // 주문자 연락처
                'buyer_email' => ['nullable', 'email', 'max:255'],
                'buyer_address_zipcode' => ['nullable', 'string', 'max:10'],
                'buyer_address' => ['nullable', 'string', 'max:255'],
                'buyer_address_detail' => ['nullable', 'string', 'max:255'],


                // 배송지 정보
                'delivery_name' => ['required', 'string', 'max:255'],
                'delivery_phone' => ['required', 'string', 'max:20'],
                'delivery_postal_code' => ['required', 'string', 'max:10'],
                'delivery_address' => ['required', 'string', 'max:255'],
                'delivery_address_detail' => ['nullable', 'string', 'max:255'],
                'delivery_request' => ['nullable', 'string'],
                'common_entrance_method' => ['nullable', 'string', 'max:255'],

                // 주문 금액 관련
                'total_amount' => ['required', 'integer', 'min:0'],//주문총액
                'delivery_fee' => ['nullable', 'integer', 'min:0'],//배송비
                'user_coupon_id' => ['nullable', //'exists:user_coupons,id'
                    /* 사용자가 발급받은 쿠폰이 있는지 확인
                    function ($attribute, $value, $fail) {
                        $isValid = UserCoupon::mine()->unused()->findOrFail($value)->exists();
                        if (!$isValid) {
                            $fail("The selected coupon is invalid for the current user.");
                        }
                    },
                    */
                ],//사용자 쿠폰 기본키
                'coupon_discount_amount' => ['nullable', 'integer', 'min:0'],//쿠폰할인액
                'use_points' => ['nullable', 'integer', 'min:0'],//적립금 사용액
                'price' => ['required', 'integer', 'min:0'],//최종결제액 = total_amount - coupon_discount_amount - use_points + delivery_fee

                'merchant_uid' => ['required'],
                'pay_method_pg' => ['required'],
                'pay_method_method' => ['required', 'in:' . implode(',', IamportMethod::values())],
            ];
        }

        return $return;
    }

    public function prepareForValidation()
    {
        //$inputs = $this->input();

        $inputs = ['user_id' => auth()->id() ?? null, 'guest_id' => $this->guest_id ?? null];

        if ($this->isMethod('POST')) {
            $inputs['status'] = OrderStatus::ORDER_PENDING->value;

            //원래가격 입력
            $productOptionIds = array_column($this->order_products, 'product_option_id');
            $productOptions = ProductOption::whereIn('id', $productOptionIds)->get()->keyBy('id');
            foreach ($this->order_products as $k => $orderProduct) {
                if (isset($productOptions[$orderProduct['product_option_id']])) {
                    /*$inputs['order_products'][$k] = [
                        ...$orderProduct,
                        'status' => $inputs['status'],
                        'user_id' => auth()->id() ?? null,
                        'guest_id' => $this->guest_id ?? null,
                        'original_price' => $productOptions[$orderProduct['product_option_id']]->original_price
                    ];*/
                    $inputs['order_products'][$k] = Order::setOrderProducts([
                        $inputs['status'], $inputs['user_id'], $inputs['guest_id'],
                        $orderProduct['product_id'], $orderProduct['product_option_id'],
                        $orderProduct['quantity'], $orderProduct['price'],
                        $productOptions[$orderProduct['product_option_id']]->original_price
                    ]);
                } else {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException("ProductOption not found for ID: " . $orderProduct['product_option_id']);
                }
            }
        }
        if ($this->isMethod('PUT')) {
            $inputs['status'] = OrderStatus::ORDER_COMPLETE->value;
            $inputs['merchant_uid'] = 'ORD-' . date('YmdHis') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
            $inputs['pay_method_pg'] = (IamportMethod::from($this->pay_method_method)) ? IamportMethod::from($this->pay_method_method)->pg() : null;
        }


        $this->merge($inputs);
    }

    public function bodyParameters(): array
    {
        return [
            'id' => ['description' => '<span class="point"></span>'],
            'user_id' => ['description' => '<span class="point">사용자 외래키</span>'],
            'guest_id' => ['description' => '<span class="point">비회원 아이디</span>'],
            'status' => ['description' => '<span class="point">주문 상태</span>'],
            'delivery_started_at' => ['description' => '<span class="point">배송 시작 일시</span>'],
            'purchase_confirmed_at' => ['description' => '<span class="point">주문 확정 일시</span>'],
            'buyer_name' => ['description' => '<span class="point">주문자 이름</span>'],
            'buyer_email' => ['description' => '<span class="point">주문자 이메일</span>'],
            'buyer_contact' => ['description' => '<span class="point">주문자 연락처</span>'],
            'buyer_address_zipcode' => ['description' => '<span class="point">주문자 우편번호</span>'],
            'buyer_address' => ['description' => '<span class="point">주문자 주소</span>'],
            'buyer_address_detail' => ['description' => '<span class="point">주문자 상세주소</span>'],
            'delivery_name' => ['description' => '<span class="point">배송지명</span>'],
            'delivery_phone' => ['description' => '<span class="point">배송지 연락처</span>'],
            'delivery_postal_code' => ['description' => '<span class="point">배송지 우편번호</span>'],
            'delivery_address' => ['description' => '<span class="point">배송지 주소</span>'],
            'delivery_address_detail' => ['description' => '<span class="point">배송지 상세주소</span>'],
            'delivery_request' => ['description' => '<span class="point">배송 요청 사항</span>'],
            'common_entrance_method' => ['description' => '<span class="point">공동현관 출입방법</span>'],
            'total_amount' => ['description' => '<span class="point">주문 총액</span>'],
            'user_coupon_id' => ['description' => '<span class="point">사용자 쿠폰 기본키</span>'],
            'coupon_discount_amount' => ['description' => '<span class="point">쿠폰 할인액</span>'],
            'use_points' => ['description' => '<span class="point">적립금 사용액</span>'],
            'delivery_fee' => ['description' => '<span class="point">배송비</span>'],
            'price' => ['description' => '<span class="point">최종결제액</span>'],
            'imp_uid' => ['description' => '<span class="point">주문번호 (아임포트)</span>'],
            'merchant_uid' => ['description' => '<span class="point">주문번호 (내부)</span>'],
            'payment_fail_reason' => ['description' => '<span class="point">결제실패사유</span>'],
            'is_payment_process_success' => ['description' => '<span class="point">결제완료처리여부</span>'],
            'is_payment_process_record' => ['description' => '<span class="point">결제 대기 또는 성공 후 관련내용 기록처리여부</span>'],
            'pay_method_pg' => ['description' => '<span class="point">결제 pg ex) html5_inicis</span>'],
            'pay_method_method' => ['description' => '<span class="point">결제수단 ex) card, vbank</span>'],
            'vbank_num' => ['description' => '<span class="point">가상계좌 계좌번호</span>'],
            'vbank_name' => ['description' => '<span class="point">가상계좌 은행명</span>'],
            'vbank_date' => ['description' => '<span class="point">가상계좌 입금기한</span>'],
            'refund_bank_name' => ['description' => '<span class="point">환불계좌 은행명</span>'],
            'refund_bank_owner' => ['description' => '<span class="point">환불계좌 예금주</span>'],
            'refund_bank_account' => ['description' => '<span class="point">환불계좌 계좌번호</span>'],
            'refund_reason' => ['description' => '<span class="point">환불사유</span>'],
            'memo' => ['description' => '<span class="point">메모</span>'],

            'order_products' => ['description' => '<span class="point">주문상품들</span>'],
            'order_products.*.product_id' => ['description' => '<span class="point">주문상품 아이디</span>'],
            'order_products.*.product_option_id' => ['description' => '<span class="point">주문상품 옵션 아이디</span>'],
            'order_products.*.quantity' => ['description' => '<span class="point">주문상품 수량</span>'],
            'order_products.*.price' => ['description' => '<span class="point">주문상품 가격</span>'],
        ];
    }
}
