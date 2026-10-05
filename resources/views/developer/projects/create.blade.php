@extends('layouts.developer')

@section('title', 'Add New Project')

@push('page-styles')
    @vite(['resources/css/uploader.css'])
@endpush

@push('page-scripts')
    @vite(['resources/js/uploader.js'])
@endpush

@section('content')
@include('components.panduan', ['kunci' => 'projects'])

<div class="pf-layout">
<div class="dev-card" style="min-width: 0;">
    <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data" data-preview-project>
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Title *</label>
                <input type="text" name="title" class="dev-input" value="{{ old('title') }}" required>
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Category</label>
                <input type="text" name="category" class="dev-input" value="{{ old('category') }}" placeholder="e.g. Web App, IoT">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Year</label>
                <input type="number" name="year" class="dev-input" value="{{ old('year', date('Y')) }}">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Live URL</label>
                <input type="url" name="live_url" class="dev-input" value="{{ old('live_url') }}" placeholder="https://...">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Repo URL</label>
                <input type="url" name="repo_url" class="dev-input" value="{{ old('repo_url') }}" placeholder="https://github.com/...">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Tech Stack (comma separated)</label>
            <input type="text" name="tech_stack" class="dev-input" value="{{ old('tech_stack') }}" placeholder="Laravel, React, Tailwind">
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Short Description (For Project Card)</label>
            <textarea name="short_description" class="dev-input" rows="2" maxlength="500">{{ old('short_description') }}</textarea>
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Long Description (For Detail Modal)</label>
            <textarea name="long_description" class="dev-input" rows="6">{{ old('long_description') }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 2rem;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Main Image (Hero)</label>
                @include('components.uploader', [
                    'name' => 'main_image',
                    'accept' => 'image/*',
                    'judul' => 'Gambar utama',
                    'keterangan' => 'Satu gambar — klik, seret, atau tempel',
                    'multiple' => false,
                ])
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Sort Order</label>
                <input type="number" name="sort_order" class="dev-input" value="{{ old('sort_order', 10) }}" required>
            </div>
        </div>

        {{-- Galeri, berkas, dan tautan bisa diisi sekaligus di sini. Sebelumnya
             ketiganya baru muncul setelah proyek tersimpan, sehingga membuat satu
             proyek lengkap selalu butuh dua langkah. --}}
        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Galeri Gambar</label>
            @include('components.uploader', [
                'name' => 'images[]',
                'accept' => 'image/*',
                'judul' => 'Tambah gambar galeri',
                'utama' => true,
            ])
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Berkas Pendukung</label>
            @include('components.uploader', [
                'name' => 'files[]',
                'accept' => '.pdf,.zip,.doc,.docx,.xls,.xlsx,.txt',
                'judul' => 'Tambah berkas',
                'keterangan' => 'PDF, ZIP, Word, Excel, atau teks',
            ])
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Tautan</label>
            <p style="font-size:.78rem; color:var(--dev-text-muted); margin:0 0 .6rem;">
                Ikonnya mengikuti tautannya sendiri — cukup tempelkan alamatnya.
                Label boleh dikosongkan.
            </p>
            @for ($i = 0; $i < 3; $i++)
                <div class="tautan-baris">
                    <input type="text" name="link_label[]" class="dev-input" placeholder="Label (opsional)" value="{{ old('link_label.'.$i) }}">
                    <input type="url" name="link_url[]" class="dev-input" placeholder="https://..." value="{{ old('link_url.'.$i) }}">
                    <span style="font-size:.75rem; color:var(--dev-text-muted);">#{{ $i + 1 }}</span>
                </div>
            @endfor
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="dev-btn">Create Project</button>
            <a href="{{ route('admin.projects') }}" class="dev-btn" style="background-color: transparent; color: var(--dev-text); border: 1px solid var(--dev-border); text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>

{{-- Proyek baru belum punya kartu di halaman depan, jadi pratinjaunya
     berupa modal detail proyek yang diisi dari formulir ini. --}}
@include('components.preview', ['target' => '#projects'])
</div>
@endsection
