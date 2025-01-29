<?php

namespace App\Http\Resources\BillingResource;

use Illuminate\Http\Resources\Json\JsonResource;

class UnpaidBillingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'client' => [
                'id' => $this->meterReading?->meter?->client?->id,
                'name' => $this->meterReading?->meter?->client?->fullName ?? 'N/A',
                'stallNumber' => $this->meterReading?->meter?->client?->stallNumber ?? 'N/A',
            ],
            'meter' => [
                'code' => $this->meterReading?->meter?->meterCode ?? 'N/A',
            ],
            'readings' => [
                'current' => [
                    'reading' => $this->meterReading?->reading ?? 'N/A',
                    'date' => $this->meterReading?->created_at?->format('Y-m-d') ?? 'N/A',
                ],
                'previous' => [
                    'reading' => $this->meterReading?->meter?->previousReading?->reading ?? 'N/A',
                    'date' => $this->meterReading?->meter?->previousReading?->created_at?->format('Y-m-d') ?? 'N/A',
                ],
            ],
            'consumption' => $this->meterReading?->consumption ?? 0,
            'rate' => $this->rate ?? 0,
            'totalAmount' => $this->totalAmount ?? 0,
            'billingDate' => $this->billingDate?->format('Y-m-d') ?? 'N/A',
        ];
    }
}
