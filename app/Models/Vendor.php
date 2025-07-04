<?php

namespace App\Models;

use App\Enums\VendorStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vendor extends Model
{
    protected $primaryKey = 'user_id';
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function scopeEligibleForPayout(Builder $query):Builder
    {
        return $query->where('status', VendorStatusEnum::APPROVED);
    }

}
