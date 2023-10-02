<?php

namespace TechStudio\Core\app\Models\Traits;

use TechStudio\Core\app\Models\Tag;

trait taggeable
{

    // protected $table = 'core_taggables';

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'core_taggables');
    }

}
