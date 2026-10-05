<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    public const JENIS = [
        'work' => 'Pekerjaan',
        'education' => 'Pendidikan',
        'organization' => 'Organisasi',
    ];

    protected $fillable = [
        'type', 'title', 'organization', 'location', 'employment_type', 'grade', 'link_label', 'link_url',
        'start_date', 'end_date', 'is_current', 'description', 'is_visible', 'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function scopeVisible(Builder $q): Builder
    {
        return $q->where('is_visible', true);
    }

    /** Kronologis terbalik menurut tanggal mulai — yang terbaru di atas. */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderByDesc('start_date')->orderBy('sort_order');
    }

    /** "Jan 2024 – Des 2025" / "Jan 2026 – Sekarang" (situs). PDF memakai "s/d" — lihat PdfTeks. */
    public function getPeriodeAttribute(): string
    {
        $f = fn (Carbon $d) => $d->locale('id')->translatedFormat('M Y');
        $akhir = $this->is_current || ! $this->end_date ? 'Sekarang' : $f($this->end_date);

        return $f($this->start_date) . ' – ' . $akhir;
    }

    /** "1 thn 9 bln" — dihitung sampai hari ini bila masih berjalan. */
    public function getDurasiAttribute(): string
    {
        $akhir = $this->is_current || ! $this->end_date ? now() : $this->end_date;
        $bulan = max(1, (int) $this->start_date->diffInMonths($akhir) + 1);
        $t = intdiv($bulan, 12);
        $b = $bulan % 12;

        return trim(($t ? "$t thn " : '') . ($b ? "$b bln" : ''));
    }

    /** Deskripsi dipecah per baris menjadi poin. */
    public function getPoinAttribute(): array
    {
        return collect(preg_split('/\R/', (string) $this->description))
            ->map(fn ($b) => trim(ltrim(trim($b), '-•*')))
            ->filter()->values()->all();
    }
}
