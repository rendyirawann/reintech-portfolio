<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroStat extends Model
{
    protected $fillable = [
        'counter_value',
        'suffix',
        'label',
        'auto_key',
        'sort_order',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public const OTOMATIS = [
        'years'    => 'Tahun pengalaman (isi angka = tahun mulai)',
        'projects' => 'Jumlah proyek yang tampil',
        'web'      => 'Jumlah proyek Web App',
        'mobile'   => 'Jumlah proyek Mobile App',
    ];

    /** Angka yang ditampilkan: dihitung sendiri bila statistiknya otomatis. */
    public function nilai(?\Illuminate\Support\Collection $proyek = null): int
    {
        $proyek ??= Project::visible()->get(['category']);
        $kategori = fn (string $awal) => $proyek->filter(fn ($p) => str_starts_with(strtolower((string) $p->category), $awal))->count();

        return match ($this->auto_key) {
            'years'    => max(0, (int) now()->year - (int) $this->counter_value),
            'projects' => $proyek->count(),
            'web'      => $kategori('web'),
            'mobile'   => $kategori('mobile'),
            default    => (int) $this->counter_value,
        };
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
