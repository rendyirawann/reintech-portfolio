@extends('layouts.developer')

@section('title', 'Manage Projects')

@section('content')
<div class="dev-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0;">Portfolio Projects</h3>
        <a href="{{ route('developer.projects.create') }}" class="dev-btn" style="text-decoration: none;">+ Add New Project</a>
    </div>

    <div style="overflow-x: auto;">
        <table class="dev-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Year</th>
                    <th>Order</th>
                    <th>Visibility</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                <tr>
                    <td>
                        @if($project->main_image)
                            <img src="{{ Storage::url($project->main_image) }}" alt="Thumb" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid var(--dev-border);">
                        @else
                            <div style="width: 60px; height: 40px; background: var(--dev-bg); border: 1px solid var(--dev-border); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: var(--dev-text-muted);">None</div>
                        @endif
                    </td>
                    <td style="font-weight: 500;">{{ $project->title }}</td>
                    <td style="color: var(--dev-text-muted);">{{ $project->category ?? '-' }}</td>
                    <td style="color: var(--dev-text-muted);">{{ $project->year ?? '-' }}</td>
                    <td>{{ $project->sort_order }}</td>
                    <td>
                        <form action="{{ route('developer.projects.toggle', $project->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="background: none; border: none; cursor: pointer; color: {{ $project->is_visible ? '#10b981' : '#64748b' }};">
                                {{ $project->is_visible ? 'Visible' : 'Hidden' }}
                            </button>
                        </form>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('developer.projects.edit', $project->id) }}" class="dev-btn" style="padding: 0.25rem 0.75rem; font-size: 0.75rem; text-decoration: none; margin-right: 0.5rem; display: inline-block;">Edit</a>
                        <form action="{{ route('developer.projects.destroy', $project->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Delete this project and all its gallery images?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="dev-btn dev-btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--dev-text-muted);">No projects found. Create your first project!</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
