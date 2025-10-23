<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailRequest extends FormRequest
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
            'to_email' => 'required|email|max:255',
            'from_email' => 'required|email|max:255',
            'from_name' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'willSucceed' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'to_email.required' => 'The recipient email address is required.',
            'to_email.email' => 'The recipient email address must be a valid email.',
            'from_email.required' => 'The sender email address is required.',
            'from_email.email' => 'The sender email address must be a valid email.',
            'subject.required' => 'The email subject is required.',
            'body.required' => 'The email body is required.',
            'willSucceed.boolean' => 'The willSucceed field must be true or false.',
        ];
    }
}
