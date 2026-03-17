<?php

namespace Modules\Acl\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasActiveStatus;

class Permission extends SpatiePermission
{
    use SoftDeletes, HasActiveStatus;

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
