<?php

namespace App\Http\Requests\ClientRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'min:2', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'middleName' => ['nullable', 'string', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'lastName' => ['required', 'string', 'min:2', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'suffix' => ['nullable', 'string', 'regex:/^[a-zA-Z\s.]+$/'],
            'email' => [
                'required',
                'email',
                Rule::unique('clients', 'email')->whereNull('deleted_at'),
            ],
            'address' => [
                'required',
                'string',
                'min:5',
                'max:500'
            ],
            'stallNumber' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9-]+$/',
                Rule::unique('clients', 'stallNumber')->whereNull('deleted_at'),
            ],
            'meterCode' => ['required', 'exists:meters,id'],
        ];
    }

    public function messages()
    {
        return [
            'firstName.required' => 'First name is required',
            'firstName.min' => 'First name must be at least 2 characters',
            'firstName.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes',

            'lastName.required' => 'Last name is required',
            'lastName.min' => 'Last name must be at least 2 characters',
            'lastName.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',

            'middleName.regex' => 'Middle name can only contain letters, spaces, hyphens, and apostrophes',

            'suffix.regex' => 'Suffix can only contain letters, spaces, and periods',

            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email address is already in use',

            'address.required' => 'Address is required',
            'address.min' => 'Address must be at least 5 characters',
            'address.max' => 'Address cannot exceed 500 characters',

            'stallNumber.required' => 'Stall number is required',
            'stallNumber.regex' => 'Stall number can only contain letters, numbers, and hyphens',
            'stallNumber.unique' => 'This stall number is already in use',

            'meterCode.required' => 'Please select a meter code',
            'meterCode.exists' => 'The selected meter code is invalid',
        ];
    }
}
