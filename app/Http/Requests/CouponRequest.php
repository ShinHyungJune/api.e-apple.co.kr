<?php

namespace App\Http\Requests;

use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'type' => ['required', 'in:' . implode(',', Coupon::TYPES)],
            'discount_amount' => ['required_if:type,' . Coupon::TYPE_AMOUNT, 'integer', 'min:0'],
            'minimum_purchase_amount' => ['required_if:type,' . Coupon::TYPE_AMOUNT, 'integer', 'min:0'],
            'discount_rate' => ['required_if:type,' . Coupon::TYPE_RATE, 'integer', 'min:0'],
            'usage_limit_amount' => ['required_if:type,' . Coupon::TYPE_RATE, 'integer', 'min:0'],
            'valid_days' => ['required', 'integer', 'min:0'],
            'issued_until' => ['required', 'date'],
        ];
    }
}
