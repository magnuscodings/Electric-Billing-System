<?php

namespace App\Http\Requests\BillingRequests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewBillingRequest extends FormRequest
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
            'meterReadingId' => ['required', 'exists:meter_readings,id'],
            'rate' => ['required', 'numeric', 'min:0'],
            'totalAmount' => ['required', 'numeric', 'min:0'],
            'billingDate' => ['required', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Ensure billing_date has a default value of the 15th of current month
        if (empty($this->billing_date)) {
            $this->merge([
                'billingDate' => now()->setDay(15)->format('Y-m-d')
            ]);
        }
    }
}
