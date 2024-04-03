<?php

namespace TechStudio\Core\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Core\app\Models\Follow;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        $isFollowed = 0;
        $selfFollow = 0;
        if (Auth('sanctum')->user()) {
            $user = Auth('sanctum')->user();
            
            if ($user->id === $this->user_id) {
                $selfFollow = 1;
            }

            $isFollowed = (bool) Follow::where('follower_id', $user->id)
                ->where('following_id', $this->user_id)->first();
        }

        return [
            'id' => $this->user_id,
            'selfFollow' => (bool) $selfFollow,
            'isFollowed' => (bool) $isFollowed,
            'displayName' => $this->getDisplayName(),
            'avatarUrl' => $this->userProfile ? $this->userProfile->avatar_url : $this->avatar_url,
            'followersCount' => $this->follower ? $this->follower->where('following_id', $this->user_id)->count() : 0,
            'followingCount' => $this->following ? $this->following->where('follower_id', $this->user_id)->count() : 0,
            'description' => $this->description,
        ];
    }
}
