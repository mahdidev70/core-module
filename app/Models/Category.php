<?php

namespace TechStudio\Core\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Community\app\Models\ChatRoom;
use TechStudio\Community\app\Models\Question;
use TechStudio\Lms\app\Models\Course;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'core_categories';

    protected static function boot()
    {
        parent::boot();

        if (!request()->is(['*/api/article_editor/*', '*/panel/*'])) {
            static::addGlobalScope('publiclyVisible', function (Builder $builder) {
                $builder->where('status', 'active');
            });
        }

        static::addGlobalScope('deletedCategory', function (Builder $builder) {
            $builder->where('status', '!=', 'deleted');
        });
    }

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

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function faq()
    {
        return $this->hasMany(Faq::class);
    }
}
