<?php

namespace App\Http\Resources\MeterResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Dropdown_MeterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'meterCode' => $this->meterCode,
        ];
    }
}
