<?php

namespace TechStudio\Core\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Community\app\Models\ChatRoom;
use TechStudio\Lms\app\Models\Course;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id'];
    
    protected $table = 'core_categories';

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function chatRoom() 
    {
        return $this->hasMany(ChatRoom::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
