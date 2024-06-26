<?php

namespace TechStudio\Core\app\Models;

use App\Models\User;
use Nette\NotImplementedException;
use TechStudio\Lms\app\Models\Course;
use TechStudio\Core\app\Models\Follow;
use TechStudio\Lms\app\Models\Student;
use Illuminate\Database\Eloquent\Model;
use TechStudio\Blog\app\Models\Article;
use Illuminate\Notifications\Notifiable;
use TechStudio\Community\app\Models\ChatRoom;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserProfile extends Model implements Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'core_user_profiles';

    protected $guarded = ['id'];

    protected $with = ['roles_unresolved'];

    protected $hidden = [
        'password',
    ];

    public function getDisplayName()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getUserType()
    {
        return 'User';
    }


    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class,'user_id','user_id');
    }

    public function chatRooms()
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_room_memberships', 'user_id', 'chat_room_id')->withPivot('unread_count');
    }

    public function getTag() {
        return $this->seller_panel_foreign_id ? 'فروشنده' : null;
    }

    public function roles_unresolved()
    {
        // before adding role parents recursively. Used by 'getRoles' function to generate full roles list.
        return $this->hasMany(UserRole::class, 'user_id');
    }

    public function getRoles()
    {
        $initialRoles = array_map(fn($roleObject) => $roleObject['role'], $this->roles_unresolved->toArray());
        return Roles::getChildrenRoles($initialRoles);
    }

    public function assertRole($role)
    {
        if (!in_array($role, $this->getRoles())) {
            throw new AccessDeniedHttpException("'$role' role required.");
        };
    }

    public function getAuthIdentifierName()
    {
        throw new NotImplementedException;
    }

    public function getAuthIdentifier()
    {
        throw new NotImplementedException;
    }

    public function getAuthPassword()
    {
        throw new NotImplementedException;
    }

    public function getRememberToken()
    {
        throw new NotImplementedException;
    }

    public function setRememberToken($value)
    {
        throw new NotImplementedException;
    }

    public function getRememberTokenName()
    {
        throw new NotImplementedException;
    }

    public function allRoles()
    {

        return Roles::getRoles();
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id', 'user_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function follower()
    {
        return $this->belongsto(Follow::class, 'user_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsto(Follow::class, 'user_id', 'following_id');
    }
}
