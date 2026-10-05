<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\Platform;
use Illuminate\Database\Eloquent\Builder;

class SocialLink extends Model
{
    protected $fillable = ['platform', 'label', 'url', 'icon_svg', 'is_visible', 'sort_order'];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * Platform diisi sendiri dari URL, dan ikonnya dirakit saat render.
     *
     * Kolom `icon_svg` yang lama dibiarkan ada demi data yang sudah telanjur
     * terisi, tetapi tidak lagi menjadi sumber ikon: markup SVG yang diketik
     * tangan sulit diperbarui serempak dan menjadi jalan masuk markup
     * berbahaya bila suatu saat dirender mentah.
     */
    protected static function booted(): void
    {
        $isi = static function (SocialLink $link): void {
            $link->platform = Platform::dariUrl($link->url);
        };

        static::creating($isi);
        static::updating($isi);
    }

    public function getIkonAttribute(): string
    {
        return Platform::ikon($this->platform ?: Platform::dariUrl($this->url));
    }

    public function getNamaPlatformAttribute(): string
    {
        return Platform::label($this->platform ?: Platform::dariUrl($this->url));
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
