<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroStat extends Model
{
    protected $fillable = [
        'counter_value',
        'suffix',
        'label',
        'sort_order',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
