<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroData extends Model
{
    protected $fillable = ['badge_text', 'title', 'description', 'stats'];

    protected $casts = [
        'stats' => 'array',
    ];
}
