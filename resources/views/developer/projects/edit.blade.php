@extends('layouts.developer')

@section('title', 'Edit Project: ' . $project->title)

@section('content')
<div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">

    <!-- Left: Edit Form -->
    <div class="dev-card" style="flex: 2; min-width: 300px;">
        <form action="{{ route('developer.projects.update', $project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Title *</label>
                    <input type="text" name="title" class="dev-input" value="{{ old('title', $project->title) }}" required>
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Category</label>
                    <input type="text" name="category" class="dev-input" value="{{ old('category', $project->category) }}" placeholder="e.g. Web App, IoT">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Year</label>
                    <input type="number" name="year" class="dev-input" value="{{ old('year', $project->year) }}">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Live URL</label>
                    <input type="url" name="live_url" class="dev-input" value="{{ old('live_url', $project->live_url) }}" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Repo URL</label>
                    <input type="url" name="repo_url" class="dev-input" value="{{ old('repo_url', $project->repo_url) }}" placeholder="https://github.com/...">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Tech Stack (comma separated)</label>
                <input type="text" name="tech_stack" class="dev-input" value="{{ old('tech_stack', implode(', ', $project->tech_stack ?? [])) }}" placeholder="Laravel, React, Tailwind">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Short Description (For Project Card)</label>
                <textarea name="short_description" class="dev-input" rows="2" maxlength="500">{{ old('short_description', $project->short_description) }}</textarea>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Long Description (For Detail Modal)</label>
                <textarea name="long_description" class="dev-input" rows="6">{{ old('long_description', $project->long_description) }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Main Image (Hero)</label>
                    @if($project->main_image)
                        <img src="{{ Storage::url($project->main_image) }}" alt="Main Image" style="width: 100px; height: 60px; object-fit: cover; border-radius: 4px; margin-bottom: 0.5rem; border: 1px solid var(--dev-border);">
                    @endif
                    <input type="file" name="main_image" class="dev-input" accept="image/*">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Sort Order</label>
                    <input type="number" name="sort_order" class="dev-input" value="{{ old('sort_order', $project->sort_order) }}" required>
                </div>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="dev-btn">Update Project</button>
                <a href="{{ route('developer.projects') }}" class="dev-btn" style="background-color: transparent; color: var(--dev-text); border: 1px solid var(--dev-border); text-decoration: none;">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Right: Gallery Images -->
    <div class="dev-card" style="flex: 1; min-width: 300px;">
        <h3 style="margin-bottom: 1rem;">Project Gallery ({{ $project->images->count() }})</h3>
        
        <form action="{{ route('developer.projects.images.upload', $project->id) }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--dev-border);">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Upload Images (Multiple allowed)</label>
                <input type="file" name="images[]" class="dev-input" accept="image/*" multiple required>
            </div>
            <button type="submit" class="dev-btn" style="width: 100%;">Upload Gallery Images</button>
        </form>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 1rem;">
            @foreach($project->images as $img)
                <div style="position: relative; aspect-ratio: 1; border-radius: 4px; overflow: hidden; border: 1px solid var(--dev-border);">
                    <img src="{{ Storage::url($img->image_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    <form action="{{ route('developer.projects.images.destroy', $img->id) }}" method="POST" style="position: absolute; top: 4px; right: 4px;">
                        @csrf @method('DELETE')
                        <button type="submit" style="background: rgba(239, 68, 68, 0.9); color: white; border: none; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;">×</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
