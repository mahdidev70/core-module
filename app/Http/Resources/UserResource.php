<?php

namespace TechStudio\Core\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->user_id,
            'selfFollow' => (bool) (Auth('sanctum')->user()->id === $this->user_id),
            'isFollowed' => (bool) $this->where('following_id', $this->user_id)->where('follower_id', Auth('sanctum')->user()->id) ?? null,
            'displayName' => $this->getDisplayName(),
            'avatarUrl' => $this->userProfile ? $this->userProfile->avatar_url : $this->avatar_url,
            'followersCount' => $this->follower ? $this->follower->where('following_id', $this->user_id)->count() : 0,
            'followingCount' => $this->following ? $this->following->where('follower_id', $this->user_id)->count() : 0,
            'description' => $this->description,
        ];
    }
}
