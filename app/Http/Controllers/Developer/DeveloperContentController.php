<?php

declare(strict_types=1);

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\AdminContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Teks halaman login & loader, serta kerangka panel admin (merek dan kaki).
 */
class DeveloperContentController extends Controller
{
    public function index(Request $request): View
    {
        $skema = AdminContent::skema();
        $tab = array_key_exists($request->query('tab'), $skema) ? $request->query('tab') : 'login';

        return view('developer.content', [
            'skema'    => $skema,
            'tab'      => $tab,
            'nilai'    => AdminContent::semua(),
            'petunjuk' => AdminContent::petunjuk(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $skema = AdminContent::skema();
        $request->validate(['_kelompok' => ['required', Rule::in(array_keys($skema))]]);
        $kelompok = $request->input('_kelompok');

        // Aturan dibangkitkan dari skema — satu sumber kebenaran untuk isian
        // mana yang ada dan jenisnya, bukan daftar kedua yang bisa menyimpang.
        $aturan = [];
        foreach ($skema[$kelompok]['isian'] as $kunci => [, $tipe]) {
            $aturan[$kunci] = match ($tipe) {
                'textarea' => 'nullable|string|max:600',
                'url'      => 'nullable|url|max:500',
                'toggle'   => 'nullable|in:0,1',
                default    => 'nullable|string|max:160',
            };
        }
        $data = $request->validate($aturan);

        foreach ($skema[$kelompok]['isian'] as $kunci => [, $tipe]) {
            // Kotak centang yang tidak dicentang tidak terkirim sama sekali.
            $nilai = $tipe === 'toggle'
                ? ($request->boolean($kunci) ? '1' : '0')
                : trim((string) ($data[$kunci] ?? ''));

            SiteSetting::updateOrCreate(['key' => $kunci], ['value' => $nilai]);
        }

        AdminContent::lupakanCache();

        return redirect()
            ->route('admin.content', ['tab' => $kelompok])
            ->with('success', $skema[$kelompok]['judul'] . ' disimpan.');
    }

    /**
     * Halaman login yang dirender untuk panel pratinjau.
     *
     * Hanya bisa dibuka setelah login (rute ini ada di grup admin.auth), dan
     * hanya di sini penahan click-jacking JavaScript dilonggarkan — header
     * X-Frame-Options dan frame-ancestors tetap melarang origin lain.
     */
    public function loginPreview(): View
    {
        return view('developer.auth.login', ['pratinjau' => true]);
    }
}
