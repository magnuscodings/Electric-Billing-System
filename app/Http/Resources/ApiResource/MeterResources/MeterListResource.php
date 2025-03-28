<?php

namespace App\Http\Resources\ApiResource\MeterResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeterListResource extends JsonResource
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
            'meterCode' => $this->meterCode,
            'stallNumber' => $this->stallNumber
         ];
    }
}
