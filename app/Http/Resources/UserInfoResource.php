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
            'phone' => $this->username,
            'avatarUrl' => $this->userProfile ? $this->userProfile->avatar_url:$this->avatar_url,
            'email' => $this->userProfile ? $this->userProfile->email:$this->email,
            'birthday' => $this->birthday,
            'job' => $this->job,
            'shopLink' =>$this->userProfile ? $this->userProfile?->shop_website:null,
        ];
    }
}
