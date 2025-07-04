<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryAddressRequest extends FormRequest
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
            'user_id' => ['nullable'],
            'name' => ['required', 'string', 'max:255'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'postal_code' => ['required', 'string', 'max:10'],
            'address' => ['required', 'string', 'max:255'],
            'address_detail' => ['nullable', 'string', 'max:255'],
            'delivery_request' => ['nullable', 'string'],
            'is_default' => ['boolean'],
        ];
    }

    public function prepareForValidation()
    {
        $inputs = $this->input();
        foreach ($inputs as $key => $input) {
            if ($input === 'null') $inputs[$key] = null;
            if ($input === 'true') $inputs[$key] = true;
            if ($input === 'false') $inputs[$key] = false;
        }

        //if (!auth()->user()?->is_admin) $inputs['user_id'] = auth()->id();
        if ($this->isMethod('post')) {
            $inputs['user_id'] = auth()->id();
        }

        $this->merge($inputs);
    }

    public function bodyParameters(): array
    {
        return [
            'id' => ['description' => '<span class="point">기본키</span>'],
            'name' => ['description' => '<span class="point">배송지명</span>'],
            'recipient_name' => ['description' => '<span class="point">배송받을 사람 이름</span>'],
            'phone' => ['description' => '<span class="point">연락처</span>'],
            'postal_code' => ['description' => '<span class="point">우편번호</span>'],
            'address' => ['description' => '<span class="point">주소</span>'],
            'address_detail' => ['description' => '<span class="point">상세주소</span>'],
            'delivery_request' => ['description' => '<span class="point">배송 요청 사항</span>'],
            'is_default' => ['description' => '<span class="point">기본 배송지 여부</span>'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '배송지명',
            'recipient_name' => '배송받을 사람 이름',
            'phone' => '연락처',
            'postal_code' => '우편번호',
            'address' => '주소',
            'address_detail' => '상세주소',
            'delivery_request' => '배송 요청 사항',
            'is_default' => '기본 배송지 여부',
        ];
    }

}
