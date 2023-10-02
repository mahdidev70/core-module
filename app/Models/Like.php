<?php

namespace TechStudio\Core\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $table = 'core_likes';

    protected $fillable = ['action', 'user_id'];

    public function likeable()
    {
        return $this->morphTo();
    }
}
