<?php

namespace TechStudio\Core\app\Models\Traits;

use TechStudio\Core\app\Models\Tag;

trait taggeable
{

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'core_taggable');
    }

}
