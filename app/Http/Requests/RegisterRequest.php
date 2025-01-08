<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
        if ($this->isMethod('POST')) {
            $return = [
                //'user_id' => ['required', 'string', 'max:255', 'unique:users'],
                'email' => ['required', 'string', 'email', 'unique:users'],
                //'password' => ['required', 'confirmed', Password::defaults()],
                'password' => ['required', 'confirmed',
                    //Password::min(8)->mixedCase()->letters()->numbers()->symbols()->uncompromised()
                    Password::min(8)->letters()->numbers()->symbols()
                ],
                'password_confirmation' => ['required'],
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'digits_between:10,11', 'unique:users'],
                'nickname' => ['nullable', 'string', 'max:255'],
                'is_agree_promotion' => ['nullable', 'boolean'],
                /*'is_agree_terms' => ['accepted'],
                'is_agree_privacy' => ['accepted'],*/
                //'code' => 'required|digits:6',
            ];
        }

        if ($this->isMethod('PUT')) {
            $return = [
                'email' => ['nullable', 'string', 'email', 'unique:users,email,' . auth()->id()],
                'name' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'digits_between:10,11', 'unique:users,phone,' . auth()->id()],
                'nickname' => ['nullable', 'string', 'max:255'],
                'is_agree_promotion' => ['nullable', 'boolean'],
            ];
        }

        return $return;
    }

    public function prepareForValidation()
    {
        $inputs = $this->input();
        foreach ($inputs as $key => $input) {
            if ($input === 'null') $inputs[$key] = null;
            if ($input === 'true') $inputs[$key] = true;
            if ($input === 'false') $inputs[$key] = false;
        }
        $this->merge($inputs);
    }

    public function bodyParameters()
    {
        return [
            'id' => ['description' => '<span class="point">기본키</span>'],
            'name' => ['description' => '<span class="point">성명</span>'],
            'email' => ['description' => '<span class="point">이메일</span>'],
            'password' => ['description' => '<span class="point">비밀번호</span>'],
            'password_confirmation' => ['description' => '<span class="point">비밀번호확인</span>'],
            'phone' => ['description' => '<span class="point">연락처</span>'],
            'nickname' => ['description' => '<span class="point">닉네임</span>'],
            'is_agree_promotion' => ['description' => '<span class="point">광고성 정보 수신 동의</span>'],
        ];
    }

}
