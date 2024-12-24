<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartOrderRequest extends FormRequest
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
        $return = ['cart_ids' => ['required', 'array', 'min:1']];
        if (auth()->check()) {
            $return['user_id'] = ['required', 'exists:users,id'];
        } else {
            $return['guest_id'] = ['required', 'string'];
        }
        return $return;
    }

    public function prepareForValidation()
    {
        $inputs = ['user_id' => auth()->id() ?? null, 'guest_id' => $this->guest_id ?? null];
        $this->merge($inputs);
    }

    public function bodyParameters(): array
    {
        return [
            'cart_ids' => ['description' => '<span class="point">장바구니 아이디</span>'],
            'user_id' => ['description' => '<span class="point">사용자 외래키</span>'],
            'guest_id' => ['description' => '<span class="point">비회원 아이디</span>'],
        ];
    }

}
