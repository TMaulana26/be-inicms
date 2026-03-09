<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends SpatiePermission
{
    use SoftDeletes;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'guard_name',
        'menu',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
