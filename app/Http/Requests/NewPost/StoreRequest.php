<?php

namespace App\Http\Requests\NewPost;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'social_account_ids' => 'required|array',
            'social_account_ids.*' => 'required|integer|exists:accounts,id',
            'content' => 'required|array',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'social_account_ids.required' => 'Please select at least one social media account to post from.',
            'social_account_ids.*.required' => 'Invalid social media account selection. Please choose a valid account.',
            'social_account_ids.*.integer' => 'Social media account selection must be a valid numeric identifier.',
            'social_account_ids.*.exists' => 'The selected social media account does not exist or is no longer available.',
            'content.required' => 'Your post seems a bit empty. Share your thoughts, ideas, or update by adding some content before posting.',
            'content.array' => 'Invalid content format. Please provide a valid post content.',
        ];
    }
}
