<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $table = 'core_categories';

    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    public function podcasts()
    {
        return $this->hasMany(Podcast::class);    
    }
}
