{{-- Satu kartu proyek + data modal detailnya. Dipakai render awal di
     welcome.blade.php dan oleh endpoint AJAX /proyek (ProjectListController). --}}
<div class="project-card" id="proyek-{{ $project->id }}" onclick="openProjectModal({{ $project->id }})" data-cari="{{ strtolower($project->title . ' ' . $project->category . ' ' . implode(' ', $project->tech_stack ?? [])) }}" data-judul="{{ $project->title }}">
    <div class="project-thumb" data-pf-bg="projects.{{ $project->id }}.main_image" style="background-image: url('{{ $project->main_image ? Storage::url($project->main_image) : '' }}'); background-size: cover; background-position: center;">
        @if(!$project->main_image)
        <div class="project-thumb-icon" aria-hidden="true">{{ \Illuminate\Support\Str::of($project->title)->explode(' ')->take(2)->map(fn ($k) => mb_substr($k, 0, 1))->implode('') }}</div>
        @endif
        <div class="project-thumb-glow" style="background: radial-gradient(circle, rgba(124,92,252,0.3), transparent 70%)"></div>
    </div>
    <div class="project-info">
        <div class="project-meta">
            <span class="project-year" data-pf="projects.{{ $project->id }}.year">{{ $project->year }}</span>
            <span class="project-type" data-pf="projects.{{ $project->id }}.category">{{ $project->category }}</span>
        </div>
        <h3 class="project-title" data-pf="projects.{{ $project->id }}.title">{{ $project->title }}</h3>
        <p class="project-desc" data-pf="projects.{{ $project->id }}.short_description">{{ $project->short_description }}</p>
        <div class="project-tags">
            @foreach($project->tech_stack ?? [] as $tech)
            <span class="project-tag">{{ $tech }}</span>
            @endforeach
        </div>
        <span class="project-link">View Case Study →</span>
    </div>
</div>

{{-- Data template for modal --}}
<template id="project-data-{{ $project->id }}">
    {!! json_encode([
        'title' => $project->title,
        'category' => $project->category,
        'year' => $project->year,
        'long_description' => $project->long_description,
        'tech_stack' => $project->tech_stack,
        'live_url' => $project->live_url,
        'repo_url' => $project->repo_url,
        'main_image' => $project->main_image ? Storage::url($project->main_image) : null,
        'images' => $project->images->map(fn($img) => Storage::url($img->image_path))
    ]) !!}
</template>
