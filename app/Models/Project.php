<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'tag_id',
        'project_status_id',
        'client_name',
        'project_name',
        'project_address',
        'site_area',
        'built_up_area',
        'project_thumbnail',
        'project_main_image',
        'slug',
        'is_active',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(ProjectTag::class, 'tag_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'project_status_id');
    }

    public function sliders(): HasMany
    {
        return $this->hasMany(ProjectSlider::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProjectCategory::class, 'project_project_category')
            ->withTimestamps();
    }
}
