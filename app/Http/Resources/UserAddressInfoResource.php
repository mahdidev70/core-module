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
            'id' => $this->userProfile->user_id,
            'displayName' => $this->userProfile->getDisplayName(),
            'state' => $this->userProfile->state,
            'city' => $this->userProfile->city,
            'street' => $this->userProfile->street,
            'block' => $this->userProfile->block,
            'unit' => $this->userProfile->unit,
            'postalCode' => $this->userProfile->postal_code,
        ];
    }
}
