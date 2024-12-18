<?php

namespace App\Http\Requests;

use App\Enums\ExchangeReturnStatus;
use App\Models\ExchangeReturn;
use Illuminate\Foundation\Http\FormRequest;

class ExchangeReturnRequest extends FormRequest
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
        $return = [
            'type' => ['required', 'in:' . implode(',', ExchangeReturn::TYPES)],
            'change_of_mind' => ['nullable', 'string'],
            'delivery_issue' => ['nullable', 'string'],
            'product_issue' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required']
        ];

        if (auth()->check()) {
            $return = [...$return, 'user_id' => ['required', 'exists:users,id']];
        } else {
            $return = [...$return, 'guest_id' => ['required', 'integer']];
        }

        return $return;
    }

    public function prepareForValidation(): void
    {
        $inputs = $this->input();

        $this->merge([
            'user_id' => auth()->id() ?? null,
            'guest_id' => $inputs['guest_id'] ?? null,
            'status' => ExchangeReturnStatus::RECEIVED->value
        ]);
    }

    public function bodyParameters(): array
    {
        return [
            'id' => ['description' => '<span class="point">기본키</span>'],
            'user_id' => ['description' => '<span class="point">사용자 외래키</span>'],
            'guest_id' => ['description' => '<span class="point">비회원 아이디</span>'],
            'order_id' => ['description' => '<span class="point">주문 기본키</span>'],
            'type' => ['description' => '<span class="point">요청 유형: exchange(교환) 또는 return(반품)</span>'],
            'change_of_mind' => ['description' => '<span class="point">단순변심 ex) 상품이 마음에 들지 않음, 더 저렴한 상품을 발견함</span>'],
            'delivery_issue' => ['description' => '<span class="point">배송문제 ex) 다른 상품이 배송됨, 배송된 장소에 박스가 분실됨, 다른 주소로 배송됨</span>'],
            'product_issue' => ['description' => '<span class="point">상품문제 ex) 상품의 구성품/부속품이 들어있지 않음, 상품이 설명과 다름, 상품이 파손되어 배송됨, 상품 결함/기능에 이상이 있음</span>'],
            'description' => ['description' => '<span class="point">상세설명</span>'],
            'status' => ['description' => '<span class="point">처리상태</span>'],
            'admin_notes' => ['description' => '<span class="point">관리자 메모</span>'],
            'processed_at' => ['description' => '<span class="point">처리 완료 시간</span>'],
            'created_at' => ['description' => '<span class="point"></span>'],
            'updated_at' => ['description' => '<span class="point"></span>'],
            'deleted_at' => ['description' => '<span class="point"></span>'],
        ];
    }

}
