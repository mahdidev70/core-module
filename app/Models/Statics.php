<?php

namespace TechStudio\Core\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Statics extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    
    protected $table = 'core_statics';

}
