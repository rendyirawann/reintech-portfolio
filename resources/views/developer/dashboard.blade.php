@extends('layouts.developer')

@section('title', 'Dashboard')

@section('content')
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
    
    <div class="dev-card">
        <h3 style="color: var(--dev-text-muted); font-size: 0.875rem; margin-bottom: 0.5rem;">Total Projects</h3>
        <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $stats['projects'] }}</p>
        <div style="margin-top: 1rem; font-size: 0.875rem; color: var(--dev-text-muted);">
            <span style="color: #10b981;">{{ $stats['visible'] }} visible</span> on frontend
        </div>
    </div>

    <div class="dev-card">
        <h3 style="color: var(--dev-text-muted); font-size: 0.875rem; margin-bottom: 0.5rem;">Active Sections</h3>
        <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $stats['sections'] }}</p>
        <div style="margin-top: 1rem; font-size: 0.875rem; color: var(--dev-text-muted);">
            Manage in Sections menu
        </div>
    </div>

    <div class="dev-card">
        <h3 style="color: var(--dev-text-muted); font-size: 0.875rem; margin-bottom: 0.5rem;">Social Links</h3>
        <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $stats['socials'] }}</p>
        <div style="margin-top: 1rem; font-size: 0.875rem; color: var(--dev-text-muted);">
            Visible icons on frontend
        </div>
    </div>

</div>
@endsection
