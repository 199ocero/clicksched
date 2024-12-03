<?php

namespace App\Http\Requests\Bluesky;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
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
            'display_name' => [
                'nullable',
                'string',
            ],
            'handle' => [
                'required',
                'string',
                'regex:/^([a-zA-Z0-9.-]+\.bsky\.social|[A-Za-z0-9-]{1,63}\.[A-Za-z]{2,6})$/',
                'unique:accounts,handle',
            ],
            'app_password' => [
                'required',
                'string',
                'min:8',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'handle.required' => 'A handle is required',
            'handle.regex' => 'The handle must be a valid Bluesky handle (e.g., example.bsky.social) or a valid domain (e.g., example.com)',
            'handle.unique' => 'This handle is already in use',
            'app_password.required' => 'App password is required',
            'app_password.min' => 'App password must be at least 8 characters long',
        ];
    }
}
