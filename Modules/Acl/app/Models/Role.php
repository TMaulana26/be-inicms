<?php

namespace Modules\Acl\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasActiveStatus;

use Spatie\Translatable\HasTranslations;

class Role extends SpatieRole
{
    use SoftDeletes, HasActiveStatus, HasTranslations;

    public $translatable = ['display_name'];

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
