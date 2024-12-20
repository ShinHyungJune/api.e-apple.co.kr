<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartProductOptionRequest extends FormRequest
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
        $return = ['quantity' => ['required', 'integer', 'min:1']];

        if ($this->isMethod('post')) {
            if (!auth()->check()) {
                $return = [...$return, 'guest_id' => ['required', 'string']];
            } else {
                $return = [...$return, 'user_id' => ['required', 'integer']];
            }
            return [
                ...$return,
                'product_option_id' => ['required', 'exists:product_options,id'],
            ];
        }

        return $return;

    }

    public function prepareForValidation()
    {
        if (auth()->check()) {
            $this->merge(['user_id' => auth()->id()]);
        }
    }

    public function bodyParameters(): array
    {
        return [
            'id' => ['description' => '<span class="point">기본키</span>'],
            'cart_id' => ['description' => '<span class="point">카트 외래키</span>'],
            'user_id' => ['description' => '<span class="point">사용자 외래키</span>'],
            'guest_id' => ['description' => '<span class="point">비회원 아이디</span>'],
            'product_option_id' => ['description' => '<span class="point">상품 옵션 외래키</span>'],
            'price' => ['description' => '<span class="point">가격</span>'],
            'quantity' => ['description' => '<span class="point">수량</span>'],
        ];
    }

}
