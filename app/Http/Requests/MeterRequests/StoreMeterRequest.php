<?php

namespace App\Http\Requests\MeterRequests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeterRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'meterCode' => 'required|min:1',
            'reading' => ['required', 'numeric', 'min:0'],
            'consumption' => ['required', 'numeric', 'min:0']
        ];
    }

    public function attributes(): array
    {
        return [
            'meterCode' => 'meter code',
            'reading' => 'meter reading',
            'consumption' => 'consumption'
        ];
    }
}
