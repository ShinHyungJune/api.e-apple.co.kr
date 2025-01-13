<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductReviewRequest extends FormRequest
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
        if ($this->isMethod('POST')) {
            return [
                'order_id' => ['required'],
                'order_product_id' => ['required'],
                'product_id' => ['required'],
                'product_option_id' => ['required', 'exists:product_options,id'],
                'user_id' => ['required'/*, 'exists:users,id'*/],
                'rating' => ['required', 'integer', 'min:1', 'max:5'],
                'review' => ['required', 'string'],
                //'images' => ['nullable', 'array'],
                'imgs' => ['nullable', 'array'],
            ];
        }

        if ($this->isMethod('PUT')) {
            return [
                'rating' => ['required', 'integer', 'min:1', 'max:5'],
                'review' => ['required', 'string'],
                //'images' => ['nullable', 'array'],
                'imgs' => ['nullable', 'array'],
                'imgs_remove_ids' => ['nullable', 'array'],
            ];
        }
    }

    public function prepareForValidation()
    {
        $this->merge(['user_id' => auth()->id()]);
    }

    public function bodyParameters()
    {
        return [
            'product_id' => ['description' => '<span class="point">참조 상품 ID</span>'],
            'user_id' => ['description' => '<span class="point">후기 작성 사용자 ID</span>'],
            'rating' => ['description' => '<span class="point">평점 (1~5)</span>'],
            'review' => ['description' => '<span class="point">후기 내용</span>'],
        ];
    }

}
