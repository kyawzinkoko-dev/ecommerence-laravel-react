<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VariationType extends Model implements HasMedia
{
    use InteractsWithMedia;

    public $timestamps = false;


    public function options(): HasMany
    {

        return $this->hasMany(VariationTypeOption::class, 'variation_type_id');
    }
}
