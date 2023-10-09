<?php

namespace TechStudio\Core\app\Models;

use TechStudio\Blog\app\Models\Article;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $table = 'core_tags';


    public function articles()
    {
        return $this->morphedByMany(Article::class, 'taggable','core_taggables');
    }
}
