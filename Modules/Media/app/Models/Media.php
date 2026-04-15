<?php

namespace Modules\Media\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasActiveStatus;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use SoftDeletes, HasActiveStatus;

    protected $guarded = [];

    /**
     * Get the category that the media belongs to.
     */
    public function category()
    {
        return $this->belongsTo(\Modules\Blog\Models\Category::class);
    }

    // We can add custom attributes, scopes, or relations to the Media model here if needed in the future.
    // By default, it arleady handles generic UUID/ID and all Spatie operations perfectly.
}
