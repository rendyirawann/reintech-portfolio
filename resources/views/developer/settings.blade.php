@extends('layouts.developer')

@section('title', 'Pengaturan')

@section('content')
@include('components.panduan', ['kunci' => 'settings'])

<div class="pf-layout">
<div class="dev-card" style="min-width: 0;">
    <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--dev-border); padding-bottom: 0.5rem;">Account Details</h3>
    
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        
        <div class="form-group" style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Name</label>
            <input type="text" name="name" data-preview-key="akun.name" class="dev-input" value="{{ old('name', $developer->name) }}" required>
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Email Address</label>
            <input type="email" name="email" class="dev-input" value="{{ old('email', $developer->email) }}" required>
        </div>

        <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--dev-border); padding-bottom: 0.5rem;">Change Password</h3>
        <p style="color: var(--dev-text-muted); font-size: 0.875rem; margin-bottom: 1rem;">Leave blank if you do not want to change the password.</p>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Current Password</label>
            <input type="password" name="current_password" class="dev-input">
        </div>

        <div class="form-group" style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">New Password (min 6 chars)</label>
            <input type="password" name="password" class="dev-input">
        </div>

        <div class="form-group" style="margin-bottom: 2rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="dev-input">
        </div>

        <button type="submit" class="dev-btn">Update Settings</button>
    </form>
</div>

{{-- Nama akun tampil di bilah atas panel ini sendiri. --}}
@include('components.preview', ['url' => route('admin.dashboard'), 'bukaUrl' => route('admin.dashboard'), 'target' => ''])
</div>
@endsection
