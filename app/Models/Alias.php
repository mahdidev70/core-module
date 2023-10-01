<?php

namespace TechStudio\Core\app\Models;

use TechStudio\Blog\app\Models\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alias extends Model
{
    use HasFactory;

    protected $table = 'core_aliases';

    public function articles()
    {
        return $this->morphMany(Article::class, 'author');
    }

    public function courses()
    {
        return $this->morphMany(Course::class, 'instructor');
    }

    public function users()
    {
        return $this->belongsToMany(UserProfile::class, 'alias_user_profile','alias_id','user_id');
    }

    public function getDisplayName() {
        return trim($this->name);
    }

    public function getUserType() {
        return 'Alias';
    }
}
