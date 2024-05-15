<?php

namespace TechStudio\Core\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'core_banners';

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        if (!request()->is(['*/api/panel/*',])) {
            static::addGlobalScope('published', function (Builder $builder) {
                $builder->where('status', 'published');
            });
        }
    }

}
