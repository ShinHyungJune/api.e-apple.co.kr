<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyNumberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $method = $this->route()->getActionMethod();

        switch ($method){
            case "store":
                return [
                    "phone" => "required|string|max:500",
                ];

            case "update":
                return [
                    'phone' => 'required|string|max:500',
                    'number' => 'required|string|max:500',
                ];

            default: return [

            ];
        }
    }

    public function bodyParameters()
    {
        return [
            "phone" => [
                "description" => "<span class='point'>연락처</span>"
            ],
            "number" => [
                "description" => "<span class='point'>인증번호</span>"
            ]
        ];
    }
}
