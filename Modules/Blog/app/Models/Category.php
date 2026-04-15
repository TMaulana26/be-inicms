<?php

namespace Modules\Blog\Models;

use App\Traits\HasActiveStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Blog\Database\Factories\CategoryFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes, HasActiveStatus;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    public const TYPE_POST = 'post';
    public const TYPE_MEDIA = 'media';

    /**
     * Get the posts for the category.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}
