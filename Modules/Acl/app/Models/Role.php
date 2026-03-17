<?php

namespace Modules\Acl\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasActiveStatus;

class Role extends SpatieRole
{
    use SoftDeletes, HasActiveStatus;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'guard_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
