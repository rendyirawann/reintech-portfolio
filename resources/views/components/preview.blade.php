@props([
    'target' => '#hero', // bagian halaman yang dituju saat pratinjau dibuka
    'url' => null,       // halaman yang dipratinjau; bawaannya halaman depan
    'bukaUrl' => null,   // tujuan tombol "buka di tab baru"; bawaannya sama dengan url
])

{{--
    Panel pratinjau langsung di samping formulir admin.

    Halaman depan dimuat di iframe dengan ?pf_preview=1. Setiap isian yang
    diketik dikirim ke iframe lewat postMessage, dan elemen dengan kunci yang
    cocok berubah seketika — sebelum disimpan. Kuncinya ditentukan oleh
    atribut data-preview-prefix pada formulir, ditambah nama isiannya.

    Iframe ini SE-ORIGIN, sehingga X-Frame-Options SAMEORIGIN dan CSP
    frame-ancestors 'self' yang sudah terpasang tetap mengizinkannya, tanpa
    melonggarkan perlindungan terhadap pembingkaian oleh situs lain.
--}}
@php
    $dasar = $url ?: route('home');
    $sumber = $dasar . (str_contains($dasar, '?') ? '&' : '?') . 'pf_preview=1' . $target;
    $buka = $bukaUrl ?: ($url ?: route('home') . $target);
@endphp

<div class="pf-preview" data-pf-preview data-target="{{ $target }}">
    <div class="pf-preview__bar">
        <span class="pf-preview__dots" aria-hidden="true"><i></i><i></i><i></i></span>
        <span class="pf-preview__title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
            Pratinjau langsung
        </span>
        <div class="pf-preview__tools">
            <button type="button" class="pf-preview__btn is-active" data-device="desktop" aria-label="Pratinjau desktop" aria-pressed="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M8 20h8M12 16v4"/></svg>
            </button>
            <button type="button" class="pf-preview__btn" data-device="mobile" aria-label="Pratinjau ponsel" aria-pressed="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg>
            </button>
            <button type="button" class="pf-preview__btn" data-preview-reload aria-label="Muat ulang pratinjau">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3-6.7L21 8M21 3v5h-5"/></svg>
            </button>
            <a href="{{ $buka }}" target="_blank" rel="noopener" class="pf-preview__btn" aria-label="Buka situs di tab baru">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3h7v7M10 14 21 3M19 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5"/></svg>
            </a>
        </div>
    </div>

    <div class="pf-preview__stage" data-stage>
        <iframe src="{{ $sumber }}" title="Pratinjau situs" loading="lazy" data-frame></iframe>
    </div>

    <p class="pf-preview__hint">Teks dan gambar yang Anda ubah tampil di sini seketika. Klik <b>Simpan</b> agar tersimpan di situs.</p>
</div>

@once
    @push('page-styles')
        @vite(['resources/css/preview.css'])
    @endpush
    @push('page-scripts')
        @vite(['resources/js/preview.js'])
    @endpush
@endonce
