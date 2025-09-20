<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PopBannerRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:255'],
            'started_at' => ['required', 'date'],
            'finished_at' => ['required', 'date', 'after:started_at'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'img' => ['nullable', 'image', 'max:10240'], // 10MB max
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
        $this->merge($inputs);
    }
}
