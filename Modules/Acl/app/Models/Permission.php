<?php

namespace Modules\Acl\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasActiveStatus;

use Spatie\Translatable\HasTranslations;

class Permission extends SpatiePermission
{
    use SoftDeletes, HasActiveStatus, HasTranslations;

    public $translatable = ['display_name'];

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
        'menu',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
