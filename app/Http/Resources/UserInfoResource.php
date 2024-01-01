<?php

namespace TechStudio\Core\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
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
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'phone' => $this->registration_phone_number,
            'avatarUrl' => $this->avatar_url,
            'email' => $this->email,
            'phone' => $this->registration_phone_number,
            'birthday' => $this->birthday,
            'job' => $this->job,
            'shopLink' => $this->shop_website,
        ];
    }
}
