<?php

namespace TechStudio\Core\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = 'core_banners';

    protected $guarded = ['id'];

}
