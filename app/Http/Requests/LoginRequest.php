<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            //'email' => ['required', 'string', 'email', 'max:255'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string'],
        ];
    }


    public function bodyParameters()
    {
        return [
            /*'title' => [
                'description' => 'The title of the post.',
                'example' => 'My First Post',
            ],
            'content' => [
                'description' => 'Contents of the post',
            ],*/
            //'email' => ['description' => '<span class="point">아이디</span>',],
            'username' => ['description' => '<span class="point">아이디</span>',],
            'password' => ['description' => '<span class="point">비밀번호</span>',],
        ];
    }
}
