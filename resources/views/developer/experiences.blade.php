@extends('layouts.developer')

@section('title', 'Pengalaman')

@push('page-styles')
<style>
    .isian { margin-bottom: 1rem; }
    .isian label { display: block; margin-bottom: .4rem; font-size: .84rem; font-weight: 600; color: var(--dev-text); }
    .isian-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .riwayat { list-style: none; margin: 0; padding: 0; display: grid; gap: .75rem; }
    .riwayat li { display: flex; gap: 1rem; align-items: flex-start; justify-content: space-between; padding: 1rem 1.1rem; border: 1px solid var(--dev-border); border-radius: 12px; }
    .riwayat li.is-edit { border-color: var(--dev-accent-2); }
    .riwayat small { color: var(--dev-text-muted); }
    .riwayat .aksi { display: flex; gap: .4rem; flex-shrink: 0; }
    .riwayat .aksi .dev-btn { padding: .3rem .65rem; font-size: .75rem; }
    .lencana { display: inline-block; font-size: .65rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--dev-accent-2); margin-bottom: .2rem; }
    .redup { opacity: .5; }
    @media (max-width: 640px) { .isian-2 { grid-template-columns: 1fr; } .riwayat li { flex-direction: column; } }
</style>
@endpush

@php $x = $edit; @endphp

@section('content')
@include('components.panduan', ['kunci' => 'experiences'])

<div class="pf-layout">
<div style="display:flex; flex-direction:column; gap:1.5rem; min-width:0;">
    <div class="dev-card">
        <h3 style="margin-bottom:1rem;">{{ $x ? 'Ubah riwayat' : 'Tambah riwayat' }}</h3>
        <form action="{{ $x ? route('admin.experiences.update', $x->id) : route('admin.experiences.store') }}" method="POST">
            @csrf
            @if ($x) @method('PUT') @endif

            <div class="isian-2">
                <div class="isian">
                    <label for="f-type">Jenis</label>
                    <select id="f-type" name="type" class="dev-input">
                        @foreach (\App\Models\Experience::JENIS as $k => $l)
                            <option value="{{ $k }}" @selected(old('type', $x?->type ?? 'work') === $k)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="isian">
                    <label for="f-emp">Status / jenis kerja</label>
                    <input id="f-emp" name="employment_type" class="dev-input" maxlength="60" value="{{ old('employment_type', $x?->employment_type) }}" placeholder="Penuh waktu, Kontrak, Tenaga Ahli…">
                </div>
            </div>
            <div class="isian">
                <label for="f-title">Jabatan / gelar *</label>
                <input id="f-title" name="title" class="dev-input" maxlength="160" required value="{{ old('title', $x?->title) }}" placeholder="TA Programmer · S1 Teknik Informatika">
            </div>
            <div class="isian">
                <label for="f-org">Instansi / kampus *</label>
                <input id="f-org" name="organization" class="dev-input" maxlength="160" required value="{{ old('organization', $x?->organization) }}">
            </div>
            <div class="isian-2">
                <div class="isian">
                    <label for="f-loc">Lokasi</label>
                    <input id="f-loc" name="location" class="dev-input" maxlength="160" value="{{ old('location', $x?->location) }}">
                </div>
                <div class="isian">
                    <label for="f-grade">IPK / nilai (pendidikan)</label>
                    <input id="f-grade" name="grade" class="dev-input" maxlength="40" value="{{ old('grade', $x?->grade) }}" placeholder="3.90 / 4.00">
                </div>
            </div>
            <div class="isian-2">
                <div class="isian">
                    <label for="f-start">Mulai *</label>
                    <input id="f-start" type="month" name="start_date" class="dev-input" required value="{{ old('start_date', $x?->start_date?->format('Y-m')) }}">
                </div>
                <div class="isian">
                    <label for="f-end">Selesai</label>
                    <input id="f-end" type="month" name="end_date" class="dev-input" value="{{ old('end_date', $x?->end_date?->format('Y-m')) }}">
                </div>
            </div>
            <div class="isian">
                <label style="display:flex; gap:.5rem; align-items:center; cursor:pointer;">
                    <input type="checkbox" name="is_current" value="1" @checked(old('is_current', $x?->is_current))> Masih berjalan
                </label>
            </div>
            <div class="isian">
                <label for="f-desc">Deskripsi — satu baris satu poin</label>
                <textarea id="f-desc" name="description" class="dev-input" rows="5" maxlength="4000">{{ old('description', $x?->description) }}</textarea>
            </div>
            <div class="isian-2">
                <div class="isian">
                    <label for="f-ll">Label tautan</label>
                    <input id="f-ll" name="link_label" class="dev-input" maxlength="200" value="{{ old('link_label', $x?->link_label) }}" placeholder="Jurnal: …, Sertifikat, …">
                </div>
                <div class="isian">
                    <label for="f-lu">URL tautan</label>
                    <input id="f-lu" type="url" name="link_url" class="dev-input" maxlength="500" value="{{ old('link_url', $x?->link_url) }}" placeholder="https://doi.org/…">
                </div>
            </div>
            <div style="display:flex; gap:.6rem;">
                <button type="submit" class="dev-btn">{{ $x ? 'Simpan perubahan' : 'Tambah' }}</button>
                @if ($x)<a href="{{ route('admin.experiences') }}" class="dev-btn" style="background:transparent; border:1px solid var(--dev-border); color:var(--dev-text);">Batal</a>@endif
            </div>
        </form>
    </div>

    <div class="dev-card">
        <h3 style="margin-bottom:1rem;">Riwayat ({{ $experiences->count() }})</h3>
        <ul class="riwayat">
            @forelse ($experiences as $e)
                <li class="{{ $x?->id === $e->id ? 'is-edit' : '' }} {{ $e->is_visible ? '' : 'redup' }}">
                    <div>
                        <span class="lencana">{{ \App\Models\Experience::JENIS[$e->type] ?? $e->type }}</span><br>
                        <strong>{{ $e->title }}</strong><br>
                        <small>{{ $e->organization }} · {{ $e->periode }}</small>
                    </div>
                    <div class="aksi">
                        <a href="{{ route('admin.experiences', ['edit' => $e->id]) }}" class="dev-btn">Ubah</a>
                        <form action="{{ route('admin.experiences.toggle', $e->id) }}" method="POST">@csrf<button class="dev-btn" type="submit">{{ $e->is_visible ? 'Sembunyikan' : 'Tampilkan' }}</button></form>
                        <form action="{{ route('admin.experiences.destroy', $e->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat ini?');">@csrf @method('DELETE')<button class="dev-btn dev-btn-danger" type="submit">Hapus</button></form>
                    </div>
                </li>
            @empty
                <li><small>Belum ada riwayat.</small></li>
            @endforelse
        </ul>
    </div>
</div>

@include('components.preview', ['target' => '#experience'])
</div>
@endsection
