<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Platform;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tautan sebuah proyek — demo, repo, artikel, toko aplikasi, apa pun.
 *
 * Kolom `platform` diisi OTOMATIS dari URL saat disimpan, sehingga pengelola
 * cukup menempelkan tautannya dan ikonnya mengikuti sendiri.
 */
class ProjectLink extends Model
{
    protected $fillable = ['project_id', 'label', 'url', 'platform', 'sort_order'];

    protected $casts = ['sort_order' => 'integer'];

    protected static function booted(): void
    {
        // Diisi di model, bukan di controller: dengan begitu tautan yang dibuat
        // lewat seeder, impor, atau tinker pun ikut mendapat platformnya.
        $isi = static function (ProjectLink $link): void {
            $link->platform = Platform::dariUrl($link->url);
        };

        static::creating($isi);
        static::updating($isi);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getIkonAttribute(): string
    {
        return Platform::ikon($this->platform ?? 'link');
    }
}
