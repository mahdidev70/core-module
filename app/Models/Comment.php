<?php

namespace TechStudio\Core\app\Models;


use TechStudio\Blog\app\Models\Article;

use App\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    // use HasFactory, SoftDeletes, Likeable;

    protected $table = 'core_comments';

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'user_type', 'commentable_type',
        'commentable_id', 'parent_id', 'text', 'status', 'rejection_reason', 'ip'
    ];

    public function user()
    {
        return $this->morphTo();
    }

    /**
     * The has Many Relationship
     *
     * @var array
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->where('status', 'approved');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'commentable_id');
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
