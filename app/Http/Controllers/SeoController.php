<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PortfolioIdentity;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * sitemap.xml dan robots.txt dibangkitkan, bukan berkas statis.
 *
 * Berkas statis di public/ akan usang begitu ada proyek baru, dan tidak ada
 * yang mengingatkan — situsnya tampak sehat sementara mesin pencari membaca
 * daftar lama. Dibangkitkan begini, isinya selalu mengikuti basis data.
 */
class SeoController extends Controller
{
    public function sitemap(): Response
    {
        // Hanya kolom yang dipakai yang diambil. Halaman ini bisa dipanggil
        // perayap berkali-kali; membaca long_description yang panjang untuk
        // sesuatu yang tidak ditampilkan adalah pemborosan yang berulang.
        $projects = Project::query()
            ->visible()
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get();

        $terbaru = $projects->max('updated_at') ?? now();

        $xml = view('partials.sitemap', compact('projects', 'terbaru'))->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /** Manifest PWA — namanya mengikuti identitas yang disunting di admin. */
    public function manifest(): JsonResponse
    {
        $identity = PortfolioIdentity::instance();

        return response()->json([
            'name' => $identity->meta_title ?: ($identity->logo_text ?? 'REINTECH'),
            'short_name' => $identity->logo_text ?? 'REINTECH',
            'description' => $identity->meta_description,
            'start_url' => url('/'),
            'display' => 'standalone',
            'background_color' => '#0B0B10',
            'theme_color' => '#0B0B10',
            'icons' => [
                ['src' => asset('images/favicon-192.png'), 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => asset('images/favicon-512.png'), 'sizes' => '512x512', 'type' => 'image/png'],
            ],
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function robots(): Response
    {
        $isi = implode("\n", [
            'User-agent: *',
            // Panel admin tidak boleh masuk indeks. Ini bukan pengamanan —
            // pengamanannya ada di middleware auth — melainkan supaya halaman
            // login tidak muncul di hasil pencarian.
            'Disallow: /admin',
            'Allow: /',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
            '',
        ]);

        return response($isi, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
