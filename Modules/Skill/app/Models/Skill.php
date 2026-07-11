<?php

namespace Modules\Skill\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Skill\Database\Factories\SkillFactory;

class Skill extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'skills';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'category',
        'order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function newFactory(): SkillFactory
    {
        return SkillFactory::new();
    }
}
