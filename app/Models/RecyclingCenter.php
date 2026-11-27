<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecyclingCenter extends Model
{
    use SoftDeletes;

    // The materials a center can accept (the checkboxes on the center form and the map filter).
    public const SERVICES = [
        'Paper', 'Metal', 'Textiles (Clothing and Fabric)', 'Glass', 'Plastic', 'Electronics (E-Waste)',
        'Cardboard', 'Batteries', 'Tires', 'Used Cooking Oil',
    ];

    public function ownerInfo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'recycling_center');
    }
}
