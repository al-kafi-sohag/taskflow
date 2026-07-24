<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Database\Factories\ProjectsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['title', 'slug', 'description', 'status'])]
#[UseFactory(ProjectsFactory::class)]

class Projects extends BaseModel
{
    use HasFactory, SoftDeletes, HasSlug;

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_projects', 'project_id', 'task_id')
            ->using(TaskProject::class)
            ->withTimestamps();
    }
}
