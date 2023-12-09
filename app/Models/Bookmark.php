<?php

namespace TechStudio\Core\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    protected $table = 'core_bookmarks';

    protected $fillable=['bookmarkable_id','bookmarkable_type','user_id'];
    
    public function bookmarkable()
    {
        return $this->morphTo();
    }
}
