<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecyclingCenter extends Model
{
    use SoftDeletes;

    public function ownerInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner');
    }
}
