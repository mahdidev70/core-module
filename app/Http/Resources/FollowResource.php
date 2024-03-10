<?php

namespace TechStudio\Core\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->userFollower->user_id ? $this->userFollowing->user_id : $this->userFollower->user_id,
            'displayName' => $this->userFollower->getDisplayName() ? $this->userFollowing->getDisplayName() : $this->userFollower->getDisplayName(),
            'avatarUrl' => $this->userFollower ? $this->userFollower->avatar_url:$this->avatar_url,
            'description' => $this->userFollower->description ? $this->userFollowing->description : $this->userFollower->description,
        ];
    }
}
