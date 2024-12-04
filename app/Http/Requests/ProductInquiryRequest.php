<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductInquiryRequest extends FormRequest
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'is_visible' => ['required', 'boolean'],
            'answer' => ['nullable', 'string'],
            'answered_at' => ['nullable'],
        ];
    }

    public function prepareForValidation()
    {
        $inputs = $this->input();
        if ($inputs['answer']) {
            $inputs['answered_at'] = now();
        }
        $this->merge($inputs);
    }

    public function bodyParameters(): array
    {
        return [
            'product_id' => ['description' => '<span class="point">참조 상품 ID</span>'],
            'user_id' => ['description' => '<span class="point">문의 작성 사용자 ID</span>'],
            'title' => ['description' => '<span class="point">문의 제목</span>'],
            'content' => ['description' => '<span class="point">문의 내용</span>'],
            'is_visible' => ['description' => '<span class="point">공개 여부 (0: 비공개, 1: 공개)</span>'],
            'answer' => ['description' => '<span class="point">관리자가 작성한 답변 내용</span>'],
        ];
    }

}
