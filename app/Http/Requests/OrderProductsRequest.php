<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @deprecated
 */
class OrderProductsRequest extends FormRequest
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
        return [
            'order_products' => ['required', 'array'],
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
        ];
    }

    public function bodyParameters(): array
    {
        return [];
    }
}
