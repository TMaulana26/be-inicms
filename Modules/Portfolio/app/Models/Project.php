<?php

namespace Modules\Portfolio\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Modules\Portfolio\Database\Factories\ProjectFactory;

class Project extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'portfolio_projects';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'tech_stack',
        'github_url',
        'demo_url',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'tech_stack' => 'array',
        'is_active' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('screenshot')
            ->singleFile();
    }

    protected static function newFactory(): ProjectFactory
    {
        return ProjectFactory::new();
    }
}
