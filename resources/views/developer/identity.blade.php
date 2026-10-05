@extends('layouts.developer')

@section('title', 'Identity Settings')

@push('page-styles')
    @vite(['resources/css/uploader.css'])
@endpush

@push('page-scripts')
    @vite(['resources/js/uploader.js'])
@endpush

@section('content')
@include('components.panduan', ['kunci' => 'identity'])

<div class="pf-layout">
<div class="dev-card" style="min-width: 0;">
    <form action="{{ route('admin.identity.update') }}" method="POST" enctype="multipart/form-data" data-preview-prefix="identity.">
        @csrf
        
        <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--dev-border); padding-bottom: 0.5rem;">Logo</h3>
        
        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="logo_text" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Logo Text (Max 10 chars)</label>
            <input type="text" id="logo_text" name="logo_text" class="dev-input" value="{{ old('logo_text', $identity->logo_text) }}" required>
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label for="logo_subtext" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Logo Subtext</label>
            <input type="text" id="logo_subtext" name="logo_subtext" class="dev-input" value="{{ old('logo_subtext', $identity->logo_subtext) }}">
        </div>

        <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--dev-border); padding-bottom: 0.5rem;">Topbar</h3>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="topbar_status_text" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Status Text</label>
            <input type="text" id="topbar_status_text" name="topbar_status_text" class="dev-input" value="{{ old('topbar_status_text', $identity->topbar_status_text) }}">
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label for="topbar_role_text" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Role Text</label>
            <input type="text" id="topbar_role_text" name="topbar_role_text" class="dev-input" value="{{ old('topbar_role_text', $identity->topbar_role_text) }}">
        </div>

        <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--dev-border); padding-bottom: 0.5rem;">Sidebar Icon</h3>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Icon Type</label>
            <div style="display: flex; gap: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--dev-text);">
                    <input type="radio" name="sidebar_icon_type" value="text" {{ old('sidebar_icon_type', $identity->sidebar_icon_type) === 'text' ? 'checked' : '' }}> Text
                </label>
                <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--dev-text);">
                    <input type="radio" name="sidebar_icon_type" value="image" {{ old('sidebar_icon_type', $identity->sidebar_icon_type) === 'image' ? 'checked' : '' }}> Image
                </label>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="sidebar_icon_value_text" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Icon Text (if type=text)</label>
            <input type="text" id="sidebar_icon_value_text" name="sidebar_icon_value_text" class="dev-input" value="{{ $identity->sidebar_icon_type === 'text' ? $identity->sidebar_icon_value : 'RD' }}">
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label for="sidebar_icon_image" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Icon Image (if type=image)</label>
            @if($identity->sidebar_icon_type === 'image' && $identity->sidebar_icon_value)
                <div style="margin-bottom: 0.5rem;">
                    <img src="{{ Storage::url($identity->sidebar_icon_value) }}" alt="Current Icon" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 1px solid var(--dev-border);">
                </div>
            @endif
            <input type="file" id="sidebar_icon_image" name="sidebar_icon_image" class="dev-input" accept="image/*">
        </div>

        <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--dev-border); padding-bottom: 0.5rem;">Contact & Footer</h3>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="contact_email" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Contact Email (Visible in Contact Section)</label>
            <input type="email" id="contact_email" name="contact_email" class="dev-input" value="{{ old('contact_email', $identity->contact_email) }}">
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label for="contact_whatsapp" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">WhatsApp Number (e.g. 6281234567890)</label>
            <input type="text" id="contact_whatsapp" name="contact_whatsapp" class="dev-input" value="{{ old('contact_whatsapp', $identity->contact_whatsapp) }}">
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label for="footer_text" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Footer Text</label>
            <input type="text" id="footer_text" name="footer_text" class="dev-input" value="{{ old('footer_text', $identity->footer_text) }}">
        </div>

        <hr style="border:0; border-top:1px solid var(--dev-border); margin:1.5rem 0;">
        <h3 style="margin:0 0 .25rem; font-size:1rem;">Data Diri</h3>
        <p style="font-size:.78rem; color:var(--dev-text-muted); margin:0 0 1rem;">
            Dipakai di bagian Tentang pada halaman depan dan di dokumen portofolio yang diekspor.
        </p>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
            <div class="form-group">
                <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Nama Lengkap</label>
                <input type="text" name="full_name" class="dev-input" value="{{ old('full_name', $identity->full_name) }}" placeholder="Rendy Irawan">
            </div>
            <div class="form-group">
                <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Lokasi</label>
                <input type="text" name="location" class="dev-input" value="{{ old('location', $identity->location) }}" placeholder="Medan, Indonesia">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
            <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Headline</label>
            <input type="text" name="headline" class="dev-input" value="{{ old('headline', $identity->headline) }}" placeholder="Full-Stack Developer & Tech Creator">
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
            <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Ringkasan profil</label>
            <textarea name="summary" class="dev-input" rows="5" maxlength="3000">{{ old('summary', $identity->summary) }}</textarea>
            <span class="pf-hint">Tampil di kartu profil halaman depan dan menjadi paragraf pembuka CV &amp; Resume. Pisahkan paragraf dengan baris baru.</span>
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
            <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Bahasa</label>
            <input type="text" name="languages" class="dev-input" maxlength="255" value="{{ old('languages', $identity->languages) }}" placeholder="Indonesia, Inggris">
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
            <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">LinkedIn</label>
            <input type="url" name="contact_linkedin" class="dev-input" value="{{ old('contact_linkedin', $identity->contact_linkedin) }}" placeholder="https://linkedin.com/in/...">
        </div>

        <div class="form-group" style="margin-bottom:1.5rem;">
            <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Foto Profil</label>
            @if ($identity->profile_image)
                <img src="{{ Storage::url($identity->profile_image) }}" alt="Foto profil"
                     style="width:96px; height:96px; object-fit:cover; border-radius:12px; margin-bottom:.6rem; border:1px solid var(--dev-border);">
            @endif
            @include('components.uploader', [
                'name' => 'profile_image',
                'accept' => 'image/*',
                'judul' => 'Foto profil',
                'keterangan' => 'Klik, seret, atau salin fotonya lalu tekan Ctrl+V di sini',
                'multiple' => false,
                'utama' => true,
            ])
        </div>

        <hr style="border:0; border-top:1px solid var(--dev-border); margin:1.5rem 0;">
        <h3 style="margin:0 0 .25rem; font-size:1rem;">SEO</h3>
        <p style="font-size:.78rem; color:var(--dev-text-muted); margin:0 0 1rem;">
            Dikosongkan pun aman — judul dan deskripsi akan mundur ke logo dan teks hero.
        </p>

        <div class="form-group" style="margin-bottom:1rem;">
            <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Judul Halaman (title)</label>
            <input type="text" name="meta_title" class="dev-input" value="{{ old('meta_title', $identity->meta_title) }}" maxlength="255">
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
            <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Deskripsi (meta description)</label>
            <textarea name="meta_description" class="dev-input" rows="2" maxlength="320">{{ old('meta_description', $identity->meta_description) }}</textarea>
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
            <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Kata Kunci</label>
            <input type="text" name="meta_keywords" class="dev-input" value="{{ old('meta_keywords', $identity->meta_keywords) }}" placeholder="laravel, full-stack, indonesia">
        </div>

        <div class="form-group" style="margin-bottom:1.5rem;">
            <label style="display:block; margin-bottom:.5rem; color:var(--dev-text-muted);">Gambar Pratinjau Tautan (OG image)</label>
            <p style="font-size:.74rem; color:var(--dev-text-muted); margin:0 0 .5rem;">
                Yang muncul saat tautan situs ini dibagikan di WhatsApp atau LinkedIn. Ideal 1200×630.
            </p>
            @if ($identity->og_image)
                <img src="{{ Storage::url($identity->og_image) }}" alt="OG image"
                     style="width:180px; aspect-ratio:1200/630; object-fit:cover; border-radius:8px; margin-bottom:.6rem; border:1px solid var(--dev-border);">
            @endif
            @include('components.uploader', [
                'name' => 'og_image',
                'accept' => 'image/*',
                'judul' => 'Gambar pratinjau',
                'multiple' => false,
            ])
        </div>

        <button type="submit" class="dev-btn">Save Identity Settings</button>
    </form>
</div>

@include('components.preview', ['target' => '#hero'])
</div>
@endsection
