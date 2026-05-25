@extends('layouts.developer')

@section('title', 'Social Links')

@section('content')
<div style="display: flex; gap: 2rem; align-items: flex-start;">
    
    <div class="dev-card" style="flex: 2;">
        <h3 style="margin-bottom: 1.5rem;">Current Links</h3>
        <div style="overflow-x: auto;">
            <table class="dev-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Platform</th>
                        <th>Label</th>
                        <th>Visibility</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($socials as $social)
                    <tr>
                        <td>{{ $social->sort_order }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 24px; height: 24px; color: var(--dev-text);">
                                    {!! $social->icon_svg !!}
                                </div>
                                {{ ucfirst($social->platform) }}
                            </div>
                        </td>
                        <td><a href="{{ $social->url }}" target="_blank" style="color: var(--dev-accent); text-decoration: none;">{{ $social->label }}</a></td>
                        <td>
                            <form action="{{ route('developer.identity.socials.toggle', $social->id) }}" method="POST">
                                @csrf
                                <button type="submit" style="background: none; border: none; cursor: pointer; color: {{ $social->is_visible ? '#10b981' : '#64748b' }};">
                                    {{ $social->is_visible ? 'Visible' : 'Hidden' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <button type="button" class="dev-btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="editSocial({{ json_encode($social) }})">Edit</button>
                                <form action="{{ route('developer.identity.socials.destroy', $social->id) }}" method="POST" onsubmit="return confirm('Delete this link?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dev-btn dev-btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="dev-card" style="flex: 1;">
        <h3 id="form-title" style="margin-bottom: 1.5rem;">Add New Link</h3>
        <form id="social-form" action="{{ route('developer.identity.socials.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="platform" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Platform ID</label>
                <input type="text" id="platform" name="platform" class="dev-input" placeholder="e.g. youtube" required>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="label" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Display Label</label>
                <input type="text" id="label" name="label" class="dev-input" placeholder="e.g. YouTube" required>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="url" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">URL</label>
                <input type="url" id="url" name="url" class="dev-input" placeholder="https://..." required>
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="sort_order" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" class="dev-input" value="10" required>
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="icon_svg" style="display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted);">SVG Icon Code</label>
                <textarea id="icon_svg" name="icon_svg" class="dev-input" rows="4" placeholder="<svg>...</svg>" required></textarea>
                <small style="color: var(--dev-text-muted); display: block; margin-top: 0.5rem;">Get icons from simpleicons.org or heroicons.com</small>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="submit" id="submit-btn" class="dev-btn" style="flex: 2;">Add Social Link</button>
                <button type="button" id="cancel-btn" class="dev-btn" style="flex: 1; background: transparent; border: 1px solid var(--dev-border); color: var(--dev-text); display: none;" onclick="resetForm()">Cancel</button>
            </div>
        </form>
    </div>

</div>

<script>
    function editSocial(social) {
        const form = document.getElementById('social-form');
        const title = document.getElementById('form-title');
        const method = document.getElementById('form-method');
        const submitBtn = document.getElementById('submit-btn');
        const cancelBtn = document.getElementById('cancel-btn');

        title.innerText = 'Edit Social Link: ' + social.label;
        form.action = "{{ url('developer-access/identity/socials') }}/" + social.id;
        method.value = 'PUT';
        submitBtn.innerText = 'Update Social Link';
        cancelBtn.style.display = 'block';

        document.getElementById('platform').value = social.platform;
        document.getElementById('label').value = social.label;
        document.getElementById('url').value = social.url;
        document.getElementById('sort_order').value = social.sort_order;
        document.getElementById('icon_svg').value = social.icon_svg;

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        const form = document.getElementById('social-form');
        const title = document.getElementById('form-title');
        const method = document.getElementById('form-method');
        const submitBtn = document.getElementById('submit-btn');
        const cancelBtn = document.getElementById('cancel-btn');

        title.innerText = 'Add New Link';
        form.action = "{{ route('developer.identity.socials.store') }}";
        method.value = 'POST';
        submitBtn.innerText = 'Add Social Link';
        cancelBtn.style.display = 'none';

        form.reset();
    }
</script>
@endsection
