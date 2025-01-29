<?php

namespace App\Http\Requests\MeterReadingRequests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeterReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'meterId' => ['required', 'min:1'],
            'reading' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'meterId' => 'meter',
            'reading' => 'meter reading',
        ];
    }
}
