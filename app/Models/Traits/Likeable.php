<?php

namespace TechStudio\Core\app\Models\Traits;

use App\Models\Like;
use Illuminate\Support\Facades\Auth;

trait Likeable
{
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable', 'core_likes');
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()
            ->where('action', 'like')
            ->count();
    }

    public function getDislikesCountAttribute()
    {
        return $this->likes()
            ->where('action', 'dislike')
            ->count();
    }

    public function getCurrent()
    {

    }

    public function likeBy($user_id)
    {
        if ($this->isLikedBy($user_id)) return;
        if ($record = $this->isDislikedBy($user_id)){
            $record->delete();
        }
        return $this->likes()->create([
            'action' => 'like',
            'user_id' => $user_id
        ]);
    }

    public function dislikeBy($user_id)
    {
        if ($this->isDislikedBy($user_id)) return;
        if ($record= $this->isLikedBy($user_id)){
            $record->delete();
        }

        return $this->likes()->create([
            'action' => 'dislike',
            'user_id' => $user_id
        ]);
    }

    public function clearBy($user_id)
    {
        $baseClass = 'App\Models\\'.class_basename($this);

        return $this->likes()
            ->where('user_id',$user_id)
            ->where('likeable_type',$baseClass)
            ->where('likeable_id',$this->id)
            ->delete();
    }

    public function isLikedBy( $user_id)
    {
        return $this->likes()
            ->where('action', 'like')
            ->where('user_id',$user_id)
            ->first();
    }

    public function isDislikedBy( $user_id)
    {
        return $this->likes()
            ->where('action', 'dislike')
            ->where('user_id',$user_id)
            ->first();
    }

    public function current_user_feedback() {
        $baseClass = 'App\Models\\'.class_basename($this);

        return $this->likes()
            ->where('user_id', Auth::user()?->id)
            ->where('likeable_type',$baseClass)
            ->where('likeable_id',$this->id)
            ->pluck('action')
            ->first();
    }
}
