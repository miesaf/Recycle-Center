<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecyclingCenter extends Model
{
    use SoftDeletes;

    public function ownerInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'recycle_center');
    }
}
