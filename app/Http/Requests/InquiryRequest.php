<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //'purchase_related_inquiry' => ['nullable', 'string'],
            //'general_consultation_inquiry' => ['nullable', 'string'],
            'type' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            //'images' => ['nullable', 'array'],
            'imgs' => ['nullable', 'array'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'id' => ['description' => '<span class="point">기본키</span>'],
            'user_id' => ['description' => '<span class="point"></span>'],
            'type' => ['description' => '<span class="point">문의 구분</span>'],
            //'purchase_related_inquiry' => ['description' => '<span class="point">구매관련문의 ex) 배송문의, 주문문의, 취소문의, 교환문의, 환불문의, 입금문의</span>'],
            //'general_consultation_inquiry' => ['description' => '<span class="point">일반상담문의 ex) 회원정보, 결제문의, 상품문의, 쿠폰/마일리지, 기타</span>'],
            'content' => ['description' => '<span class="point">문의 내용</span>'],
            'answer' => ['description' => '<span class="point">관리자 답변</span>'],
            'answered_at' => ['description' => '<span class="point">답변 일시</span>'],
        ];
    }
}
