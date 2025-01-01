<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    function parent() : BelongsTo {
        return $this->belongsTo(Category::class,'parent_id');
    }
}
