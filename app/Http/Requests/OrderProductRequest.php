<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;

class OrderProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ids' => ['nullable', 'array'],
            'delivery_company' => ['required', 'string'],
            'delivery_tracking_number' => ['required', 'string'],
            'delivery_started_at' => ['required', 'date'],
            'status' => ['required'],
        ];
    }

    public function prepareForValidation()
    {
        $inputs = $this->input();
        $inputs['delivery_started_at'] = $inputs['delivery_started_at'] ?? now();
        $inputs['status'] = OrderStatus::DELIVERY->value;
        $this->merge($inputs);
    }

}
