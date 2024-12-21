<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
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
        $return = [];

        if (auth()->check()) {
            $return = [...$return, 'user_id' => ['required', 'exists:users,id']];
        } else {
            $return = [...$return, 'guest_id' => ['required', 'string']];
        }

        return [
            ...$return,
            'product_id' => ['required', 'exists:products,id'],
            'product_options' => ['required', 'array'],
            'product_options.*.product_option_id' => ['required', 'integer'],
            'product_options.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => auth()->id() ?? null,
            'guest_id' => $inputs['guest_id'] ?? null,
        ]);
    }

    public function bodyParameters(): array
    {
        return [
            'id' => ['description' => '<span class="point">기본키</span>'],
            'user_id' => ['description' => '<span class="point">사용자 외래키</span>'],
            'guest_id' => ['description' => '<span class="point">비회원 아이디</span>'],
            'product_id' => ['description' => '<span class="point">상품 외래키</span>'],
        ];
    }

}
