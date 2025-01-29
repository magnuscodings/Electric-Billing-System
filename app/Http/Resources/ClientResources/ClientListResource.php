<?php

namespace App\Http\Resources\ClientResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fullName' => $this->fullName,
            'address' => $this->address,
            'code' => $this->meter,
            'stallNumber' => $this->stallNumber
        ];
    }
}
