<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TechStack extends Model
{
    public const KELOMPOK = ['Backend', 'Frontend', 'Mobile', 'Database', 'Tools'];

    protected $fillable = ['name', 'slug', 'color', 'group', 'is_visible', 'sort_order'];

    protected $casts = ['is_visible' => 'boolean'];

    public function scopeVisible(Builder $q): Builder
    {
        return $q->where('is_visible', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('name');
    }

    /** Ikon disimpan lokal di public/images/tech (lihat TechIcon::unduh). */
    public function getIkonUrlAttribute(): ?string
    {
        return $this->slug && is_file(public_path("images/tech/{$this->slug}.svg"))
            ? asset("images/tech/{$this->slug}.svg")
            : null;
    }
}
