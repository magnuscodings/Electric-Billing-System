<?php

namespace App\Http\Resources\ClientResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'middleName' => $this->middleName,
            'lastName' => $this->lastName,
            'suffix' => $this->suffix,
            'contactDetails' => [
                'address' => $this->address,
            ],
            'billingDetails' => [
                'code' => $this->meterCode,
                'stallNumber' => $this->stallNumber,
            ],
            'timestamps' => [
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
                'formattedCreated' => $this->created_at?->format('F d, Y H:i:s'),
                'formattedUpdated' => $this->updated_at?->format('F d, Y H:i:s'),
            ]
        ];
    }
}
