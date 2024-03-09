<?php

namespace TechStudio\Core\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserKnsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'info' => [
                'id' => $this->id,
                'displayName' => $this->getDisplayName(),
                'avatarUrl' => $this->userProfile ? $this->userProfile->avatar_url:$this->avatar_url,
                'followersCount' => 10,
                'followingCount' => 4,
                'description' => $this->descripiton,
            ],
            'followers' => [
                
            ]
        ];
    }
}
