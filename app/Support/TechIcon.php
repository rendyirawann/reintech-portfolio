<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Ikon tech stack dari simple-icons, diunduh SEKALI ke public/images/tech.
 *
 * Disimpan lokal supaya halaman depan dan PDF tidak bergantung pada CDN, dan
 * disaring ketat: hanya <svg> berisi <path d="..."> yang diterima, sehingga
 * berkas yang tersimpan tidak bisa membawa skrip.
 */
class TechIcon
{
    private const SUMBER = 'https://cdn.jsdelivr.net/npm/simple-icons@13/icons/%s.svg';

    public static function unduh(?string $slug): bool
    {
        if (! $slug || ! preg_match('/^[a-z0-9]+$/', $slug)) {
            return false;
        }

        $tujuan = public_path("images/tech/{$slug}.svg");
        if (is_file($tujuan)) {
            return true;
        }

        try {
            $r = Http::timeout(10)->get(sprintf(self::SUMBER, $slug));
        } catch (\Throwable $e) {
            Log::warning('TechIcon: gagal mengunduh', ['slug' => $slug, 'error' => $e->getMessage()]);
            return false;
        }

        if (! $r->ok() || ! preg_match('/<path d="([^"<>]+)"/', $r->body(), $m)) {
            return false;
        }

        @mkdir(dirname($tujuan), 0775, true);
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="' . $m[1] . '"/></svg>';

        return file_put_contents($tujuan, $svg) !== false;
    }

    /** Isi path saja, untuk disematkan inline (warna diatur CSS). */
    public static function path(?string $slug): ?string
    {
        $f = $slug ? public_path("images/tech/{$slug}.svg") : null;
        if (! $f || ! is_file($f)) {
            return null;
        }

        return preg_match('/<path d="([^"<>]+)"/', file_get_contents($f), $m) ? $m[1] : null;
    }
}
