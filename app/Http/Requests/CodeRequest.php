<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //return false;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $results = [
            'parent_id' => 'required',
            'name' => 'required',
            'is_use' => 'required',
            'is_display' => 'required',
        ];

        if ($this->method() === 'PUT') {
            $results['id'] = 'required';
        }

        return $results;
    }

    public function prepareForValidation()
    {
        //$inputs = $this->input();
        $this->merge(['is_use' => true, 'is_display' => true]);
    }

}
