@extends('layouts.developer')

@section('title', 'Add New Project')

@section('content')
<div class="dev-card" style="max-width: 800px;">
    <form action="{{ route('developer.projects.store') }}" method="POST" enctype="multipart/form-data">
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
                <input type="file" name="main_image" class="dev-input" accept="image/*">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Sort Order</label>
                <input type="number" name="sort_order" class="dev-input" value="{{ old('sort_order', 10) }}" required>
            </div>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="dev-btn">Create Project</button>
            <a href="{{ route('developer.projects') }}" class="dev-btn" style="background-color: transparent; color: var(--dev-text); border: 1px solid var(--dev-border); text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>
@endsection
