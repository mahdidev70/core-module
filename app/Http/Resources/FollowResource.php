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
        $isFollowed = 0;
        if (Auth('sanctum')->user()) {
            $user = Auth('sanctum')->user();
            $isFollowed = (boolean) Follow::where('follower_id', $user->id)->where('following_id',$this->user_id)->first();
        }
        
        return [
            'id' => $this->user_id,
            'displayName' => $this->getDisplayName(),
            'avatarUrl' => $this->avatar_url,
            'description' => $this->description,
            'isFollowed' => $isFollowed,
        ];
    }
}
