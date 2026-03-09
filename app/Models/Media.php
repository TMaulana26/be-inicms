<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use SoftDeletes;

    protected $guarded = [];

    // We can add custom attributes, scopes, or relations to the Media model here if needed in the future.
    // By default, it arleady handles generic UUID/ID and all Spatie operations perfectly.
}
