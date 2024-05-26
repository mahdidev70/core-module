<?php

namespace TechStudio\Core\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TroubleshootingReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'reportable_id',
        'reportable_type',
        'report'
    ];

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }
}