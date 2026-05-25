@extends('layouts.developer')

@section('title', 'Identity Settings')

@section('content')
<div class="dev-card" style="max-width: 600px;">
    <form action="{{ route('developer.identity.update') }}" method="POST" enctype="multipart/form-data">
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

        <button type="submit" class="dev-btn">Save Identity Settings</button>
    </form>
</div>
@endsection
