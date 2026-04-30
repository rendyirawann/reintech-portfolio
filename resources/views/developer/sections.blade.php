@extends('layouts.developer')

@section('title', 'Manage Sections')

@section('content')
<div style="display: grid; gap: 1.5rem;">
    @foreach($sections as $section)
    <div class="dev-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; border-bottom: 1px solid var(--dev-border); padding-bottom: 1rem;">
            <div>
                <h3 style="font-size: 1.25rem; margin-bottom: 0.25rem;">Section: {{ strtoupper($section->section_key) }}</h3>
                <p style="color: var(--dev-text-muted); font-size: 0.875rem;">Modify text content for this section</p>
            </div>
            <form action="{{ route('developer.sections.toggle', $section->id) }}" method="POST">
                @csrf
                <button type="submit" class="dev-btn" style="background-color: {{ $section->is_visible ? 'rgba(16, 185, 129, 0.1)' : 'rgba(100, 116, 139, 0.1)' }}; color: {{ $section->is_visible ? '#10b981' : '#64748b' }}; border: 1px solid currentColor;">
                    {{ $section->is_visible ? '👁️ Visible' : '🙈 Hidden' }}
                </button>
            </form>
        </div>

        <form action="{{ route('developer.sections.update', $section->id) }}" method="POST">
            @csrf @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Title</label>
                    <input type="text" name="title" class="dev-input" value="{{ $section->title }}">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Subtitle</label>
                    <input type="text" name="subtitle" class="dev-input" value="{{ $section->subtitle }}">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Description</label>
                <textarea name="description" class="dev-input" rows="3">{{ $section->description }}</textarea>
            </div>

            <button type="submit" class="dev-btn">Save {{ ucfirst($section->section_key) }} Section</button>
        </form>
    </div>
    @endforeach
</div>
@endsection
