<?php

namespace TechStudio\Core\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Community\app\Models\ChatRoom;
use TechStudio\Community\app\Models\Question;
use TechStudio\Lms\app\Models\Course;

class Follow extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'core_follows';

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower');
    }

    public function following()
    {
        return $this->belongsTo(User::class, 'following');
    }
}
