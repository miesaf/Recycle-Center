<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Log extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'module',
        'model_id',
        'action',
        'user',
    ];

    public function userInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }
}
