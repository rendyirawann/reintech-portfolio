@extends('layouts.developer')

@section('title', 'Dashboard')

@php
    $i = fn (string $d) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';

    $sapaan = match (true) {
        now()->hour < 11 => 'Selamat pagi',
        now()->hour < 15 => 'Selamat siang',
        now()->hour < 18 => 'Selamat sore',
        default => 'Selamat malam',
    };

    $lengkap = collect($syarat)->filter()->count();
    $persen = (int) round($lengkap / max(1, count($syarat)) * 100);
@endphp

@section('content')
@include('components.panduan', ['kunci' => 'dashboard'])

{{-- Sapaan + aksi utama --}}
<section class="dash-hero">
    <div>
        <h2>{{ $sapaan }}, {{ \Illuminate\Support\Str::of($identity->full_name ?: session('developer_name', 'Admin'))->before(' ') }}.</h2>
        <p>Portofolio Anda memuat {{ $stats['visible'] }} proyek yang tampil. Unduh sebagai PDF untuk dikirim ke klien, atau lanjutkan mengelola kontennya.</p>
    </div>
    <div class="dash-hero-actions">
        <a href="{{ route('admin.export') }}" class="dev-btn">
            {!! $i('<path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/>') !!}
            Ekspor Portofolio PDF
        </a>
        <a href="{{ route('admin.export', ['jenis' => 'cv']) }}" class="dev-btn dev-btn-ghost">CV</a>
        <a href="{{ route('admin.export', ['jenis' => 'resume']) }}" class="dev-btn dev-btn-ghost">Resume</a>
        <a href="{{ route('home') }}" target="_blank" rel="noopener" class="dev-btn dev-btn-ghost">
            {!! $i('<path d="M14 3h7v7M10 14 21 3"/>') !!}
            Lihat Situs
        </a>
    </div>
</section>

{{-- Statistik --}}
<div class="dash-stats">
    <a href="{{ route('admin.projects') }}" class="dash-stat warna-biru">
        <div class="dash-stat-ikon">{!! $i('<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/>') !!}</div>
        <div class="dash-stat-angka">{{ $stats['projects'] }}</div>
        <div class="dash-stat-label">Total proyek</div>
        <div class="dash-stat-kaki">{{ $stats['visible'] }} tampil di situs</div>
    </a>

    <a href="{{ route('admin.projects') }}" class="dash-stat warna-cyan">
        <div class="dash-stat-ikon">{!! $i('<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-5-5L5 21"/>') !!}</div>
        <div class="dash-stat-angka">{{ $stats['media'] }}</div>
        <div class="dash-stat-label">Gambar &amp; berkas</div>
        <div class="dash-stat-kaki">Terkompres otomatis</div>
    </a>

    <a href="{{ route('admin.identity.socials') }}" class="dash-stat warna-hijau">
        <div class="dash-stat-ikon">{!! $i('<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>') !!}</div>
        <div class="dash-stat-angka">{{ $stats['socials'] }}</div>
        <div class="dash-stat-label">Media sosial aktif</div>
        <div class="dash-stat-kaki">Ikon mengikuti tautan</div>
    </a>

    <a href="{{ route('admin.sections') }}" class="dash-stat warna-kuning">
        <div class="dash-stat-ikon">{!! $i('<path d="M3 5h18M3 12h18M3 19h12"/>') !!}</div>
        <div class="dash-stat-angka">{{ $stats['sections'] }}</div>
        <div class="dash-stat-label">Bagian halaman aktif</div>
        <div class="dash-stat-kaki">Hero, Tentang, Layanan…</div>
    </a>

    <div class="dash-stat warna-merah">
        <div class="dash-stat-ikon">{!! $i('<path d="M4 4h16v12H7l-3 3Z"/>') !!}</div>
        <div class="dash-stat-angka">{{ $stats['unread'] }}</div>
        <div class="dash-stat-label">Pesan belum dibaca</div>
        <div class="dash-stat-kaki">Dari formulir kontak</div>
    </div>
</div>

<div class="dash-grid">
    {{-- Proyek terbaru --}}
    <section class="dev-card">
        <div class="dash-panel-kepala">
            <h3>Proyek terakhir diubah</h3>
            <a href="{{ route('admin.projects') }}">Semua proyek</a>
        </div>

        @forelse ($terbaru as $p)
            <div class="dash-item">
                @if ($p->main_image)
                    <img class="dash-item-gambar" src="{{ Storage::url($p->main_image) }}" alt="" loading="lazy" width="46" height="46">
                @else
                    <div class="dash-item-gambar">{{ mb_strtoupper(mb_substr($p->title, 0, 2)) }}</div>
                @endif
                <div class="dash-item-teks">
                    <strong>{{ $p->title }}</strong>
                    <span>{{ collect([$p->category, $p->year])->filter()->implode(' · ') ?: 'Tanpa kategori' }} · diubah {{ $p->updated_at?->diffForHumans() }}</span>
                </div>
                <span class="dev-pil {{ $p->is_visible ? 'dev-pil-hijau' : 'dev-pil-abu' }}">{{ $p->is_visible ? 'Tampil' : 'Disembunyikan' }}</span>
                <a href="{{ route('admin.projects.edit', $p->id) }}" class="dev-icon-btn" aria-label="Sunting {{ $p->title }}">
                    {!! $i('<path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>') !!}
                </a>
            </div>
        @empty
            <div class="dash-kosong">
                Belum ada proyek.
                <div style="margin-top:.8rem;"><a href="{{ route('admin.projects.create') }}" class="dev-btn">Tambah proyek pertama</a></div>
            </div>
        @endforelse
    </section>

    <div style="display:grid; gap:1.25rem; align-content:start;">
        {{-- Kelengkapan profil --}}
        <section class="dev-card">
            <div class="dash-panel-kepala">
                <h3>Kelengkapan profil</h3>
                <span class="dev-pil {{ $persen === 100 ? 'dev-pil-hijau' : 'dev-pil-abu' }}">{{ $persen }}%</span>
            </div>
            <div style="height:8px; border-radius:99px; background:var(--dev-surface-2); overflow:hidden; margin-bottom:1rem;">
                <div style="height:100%; width:{{ $persen }}%; background:var(--dev-grad); transition:width 400ms ease;"></div>
            </div>
            @foreach ($syarat as $label => $ok)
                <div style="display:flex; align-items:center; gap:.55rem; padding:.3rem 0; font-size:.86rem; color:{{ $ok ? 'var(--dev-text)' : 'var(--dev-text-muted)' }};">
                    <span style="width:18px; height:18px; flex:none; color:{{ $ok ? 'var(--dev-green)' : 'var(--dev-text-muted)' }};">
                        {!! $ok ? $i('<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>') : $i('<circle cx="12" cy="12" r="9"/>') !!}
                    </span>
                    {{ $label }}
                </div>
            @endforeach
            @if ($persen < 100)
                <a href="{{ route('admin.identity') }}" class="dev-btn dev-btn-ghost" style="width:100%; margin-top:.9rem;">Lengkapi profil</a>
            @endif
        </section>

        {{-- Aksi cepat --}}
        <section class="dev-card">
            <div class="dash-panel-kepala"><h3>Aksi cepat</h3></div>
            <div class="dash-aksi">
                <a href="{{ route('admin.projects.create') }}" class="warna-biru">{!! $i('<path d="M12 5v14M5 12h14"/>') !!}Proyek baru</a>
                <a href="{{ route('admin.identity') }}" class="warna-cyan">{!! $i('<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>') !!}Ubah identitas</a>
                <a href="{{ route('admin.identity.socials') }}" class="warna-hijau">{!! $i('<path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"/>') !!}Tambah tautan</a>
                <a href="{{ route('admin.export') }}" class="warna-kuning">{!! $i('<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6"/>') !!}Unduh PDF</a>
            </div>
        </section>
    </div>
</div>

{{-- Pratinjau halaman depan apa adanya — pintasan untuk melihat hasil
     semua perubahan tanpa meninggalkan panel. --}}
@unless (request()->boolean('pf_preview'))
    {{-- Tidak dirender saat dashboard sendiri tampil sebagai pratinjau,
         supaya tidak ada bingkai di dalam bingkai. --}}
    <section style="margin-top:1.5rem;">
        @include('components.preview', ['target' => ''])
    </section>
@endunless
@endsection
