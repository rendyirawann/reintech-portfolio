<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioSection extends Model
{
    protected $fillable = ['section_key', 'title', 'subtitle', 'description', 'is_visible'];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public static function getByKey(string $key): ?static
    {
        return static::where('section_key', $key)->first();
    }
}
