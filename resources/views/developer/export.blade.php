@extends('layouts.developer')

@section('title', 'Ekspor Dokumen')

@push('page-styles')
<style>
    /* Gaya khusus halaman ini — cukup kecil untuk ditulis di sini. */
    .ekspor-bilah { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; }
    .ekspor-bilah p { margin: .2rem 0 0; font-size: .84rem; color: var(--dev-text-muted); }
    .ekspor-aksi { display: flex; flex-wrap: wrap; gap: .6rem; }
    .ekspor-bingkai { position: relative; height: min(82vh, 1100px); border: 1px solid var(--dev-border); border-radius: var(--dev-radius); overflow: hidden; background: #525659; }
    .ekspor-bingkai iframe { width: 100%; height: 100%; border: 0; display: block; }
    .ekspor-muat { position: absolute; inset: 0; display: grid; place-content: center; justify-items: center; gap: 1rem; background: var(--dev-bg); color: var(--dev-text-muted); font-size: .86rem; transition: opacity 300ms ease; }
    .ekspor-muat.selesai { opacity: 0; pointer-events: none; }
    .ekspor-cincin { width: 46px; height: 46px; border-radius: 50%; border: 3px solid var(--dev-border); border-top-color: var(--dev-accent-2); animation: ekspor-putar .8s linear infinite; }
    .ekspor-tab { display: flex; flex-wrap: wrap; gap: .4rem; padding: .3rem; margin-bottom: 1rem; border: 1px solid var(--dev-border); border-radius: 12px; width: fit-content; max-width: 100%; }
    .ekspor-tab a { padding: .5rem 1rem; border-radius: 9px; text-decoration: none; font-weight: 600; font-size: .86rem; color: var(--dev-text-muted); }
    .ekspor-tab a[aria-current="page"] { color: var(--dev-text); background: color-mix(in srgb, var(--dev-accent) 16%, transparent); box-shadow: inset 0 -2px 0 var(--dev-accent-2); }
    .ekspor-beda { margin: 0 0 1.25rem; font-size: .84rem; color: var(--dev-text-muted); max-width: 80ch; }
    @keyframes ekspor-putar { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
@include('components.panduan', ['kunci' => 'export'])

<nav class="ekspor-tab" aria-label="Jenis dokumen">
    @foreach ($semuaJenis as $k => $l)
        <a href="{{ route('admin.export', ['jenis' => $k]) }}" @if ($jenis === $k) aria-current="page" @endif>{{ $l }}</a>
    @endforeach
</nav>
<p class="ekspor-beda">
    @switch ($jenis)
        @case('cv') <b>CV</b> — dokumen lengkap beberapa halaman: data pribadi, seluruh riwayat kerja, pendidikan &amp; publikasi, semua proyek, dan keahlian per kelompok. Untuk lamaran instansi, beasiswa, atau akademik. @break
        @case('resume') <b>Resume</b> — satu halaman, ringkas, satu kolom agar terbaca sistem ATS. Pengalaman terpenting, keahlian inti, tiga proyek pilihan. Untuk melamar kerja di perusahaan. @break
        @case('portfolio-list') <b>Portofolio (Daftar)</b> — semua proyek dalam bentuk daftar: ringkasan, uraian, teknologi, dan tautan situs/repo. Tanpa gambar, ringan untuk dikirim lewat email. @break
        @default <b>Portofolio (Bergambar)</b> — visual: profil singkat lalu studi kasus proyek bergambar. Untuk dikirim ke klien.
    @endswitch
</p>

<div class="ekspor-bilah">
    <div>
        <strong>{{ $namaBerkas }}</strong>
        <p>Periksa dokumennya di bawah. Belum puas? Ubah datanya di Identitas &amp; SEO, Pengalaman, Tech Stack, atau Proyek, lalu muat ulang halaman ini.</p>
    </div>
    <div class="ekspor-aksi">
        <a href="{{ route('admin.export.pdf', ['jenis' => $jenis]) }}" target="_blank" rel="noopener" class="dev-btn dev-btn-ghost">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3h7v7M10 14 21 3M19 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5"/></svg>
            Buka di tab baru
        </a>
        <a href="{{ route('admin.export.pdf', ['jenis' => $jenis, 'unduh' => 1]) }}" class="dev-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/></svg>
            Unduh PDF
        </a>
    </div>
</div>

<div class="ekspor-bingkai">
    {{-- Pratinjau memakai berkas PDF yang SAMA dengan unduhan — bukan halaman
         HTML tiruan — jadi yang terlihat di sini persis yang akan diterima klien. --}}
    <iframe src="{{ route('admin.export.pdf', ['jenis' => $jenis]) }}#view=FitH" title="Pratinjau {{ $semuaJenis[$jenis] }} PDF" id="ekspor-pdf"></iframe>
    <div class="ekspor-muat" id="ekspor-muat" role="status" aria-live="polite">
        <div class="ekspor-cincin" aria-hidden="true"></div>
        <span>Menyusun dokumen…</span>
    </div>
</div>

<script>
    // Pembuatan PDF butuh beberapa detik; penutup dibuka begitu berkasnya tiba.
    document.getElementById('ekspor-pdf').addEventListener('load', function () {
        document.getElementById('ekspor-muat').classList.add('selesai');
    });
</script>
@endsection
