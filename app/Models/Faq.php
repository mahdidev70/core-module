<?php

namespace TechStudio\Core\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'core_faq';
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')
            ->where('table_type', get_class($this))
            ->select(['id', 'slug', 'title', 'description', 'avatar_url', 'status', 'created_at', 'language']);
    }

}
