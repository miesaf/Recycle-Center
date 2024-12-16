<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use SoftDeletes;

    public function centerInfo(): BelongsTo
    {
        return $this->belongsTo(RecyclingCenter::class, 'recycling_center');
    }

    public function userInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }
}
