<?php

namespace TechStudio\Core\app\Models\Traits;

use TechStudio\Core\app\Models\Bookmark;

trait Bookmarkable
{

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable', 'core_bookmarks');
    }
    public function clearBookmarkBy($user_id)
    {
        $baseClass = 'App\Models\\'.class_basename($this);
        return $this->bookmarks()
                ->where('user_id',$user_id)
            ->where('bookmarkable_type',$baseClass)
            ->where('bookmarkable_id',$this->id)
            ->delete();
    }

    public function saveBy($user_id)
    {
        $baseClass = 'App\Models\\'.class_basename($this);
        return $this->bookmarks()->create([
            'bookmarkable_id' => $this->id,
            'bookmarkable_type' => $baseClass,
            'user_id' => $user_id
        ]);
    }

    public function isSavedBy( $user_id)
    {
        return $this->bookmarks()
            ->where('user_id',$user_id)
            ->first();
    }

}
