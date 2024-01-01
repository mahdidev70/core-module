<?php

namespace TechStudio\Core\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'displayName' => $this->getDisplayName(),
            'state' => $this->state,
            'city' => $this->city,
            'street' => $this->street,
            'block' => $this->block,
            'unit' => $this->unit,
            'postalCode' => $this->postal_code,
        ];
    }
}
