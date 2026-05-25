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

            @if($section->section_key === 'contact')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 8px;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-accent);">Contact Email (Value)</label>
                    <input type="email" name="contact_email" class="dev-input" value="{{ $identity->contact_email }}" placeholder="hello@reintech.dev">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--dev-accent);">WhatsApp Number</label>
                    <input type="text" name="contact_whatsapp" class="dev-input" value="{{ $identity->contact_whatsapp }}" placeholder="6281234567890">
                </div>
            </div>
            @endif

            <button type="submit" class="dev-btn">Save {{ ucfirst($section->section_key) }} Section</button>
        </form>

        {{-- ═══════════════════════════════════════════════
             HERO STATS MANAGEMENT (inline under hero section)
        ═══════════════════════════════════════════════ --}}
        @if($section->section_key === 'hero')
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--dev-border);">
            <h4 style="margin-bottom: 1rem; color: var(--dev-accent);">📊 Hero Stats (Counter Items)</h4>

            {{-- Existing Stats --}}
            @foreach($heroStats as $stat)
            <div style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem; align-items: end;">
                <form action="{{ route('developer.sections.hero-stats.update', $stat->id) }}" method="POST" style="display: grid; grid-template-columns: 100px 80px 1fr 80px auto; gap: 0.75rem; align-items: end; flex: 1;">
                    @csrf @method('PUT')
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Value</label>
                        <input type="number" name="counter_value" class="dev-input" value="{{ $stat->counter_value }}" style="padding: 0.4rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Suffix</label>
                        <input type="text" name="suffix" class="dev-input" value="{{ $stat->suffix }}" style="padding: 0.4rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Label</label>
                        <input type="text" name="label" class="dev-input" value="{{ $stat->label }}" style="padding: 0.4rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Order</label>
                        <input type="number" name="sort_order" class="dev-input" value="{{ $stat->sort_order }}" style="padding: 0.4rem;">
                    </div>
                    <button type="submit" class="dev-btn" style="padding: 0.4rem 0.75rem; font-size: 0.75rem;">Save</button>
                </form>
                <form action="{{ route('developer.sections.hero-stats.destroy', $stat->id) }}" method="POST" onsubmit="return confirm('Delete this stat?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="dev-btn dev-btn-danger" style="padding: 0.4rem 0.75rem; font-size: 0.75rem;">×</button>
                </form>
            </div>
            @endforeach

            {{-- Add New Stat --}}
            <form action="{{ route('developer.sections.hero-stats.store') }}" method="POST" style="display: grid; grid-template-columns: 100px 80px 1fr 80px auto; gap: 0.75rem; align-items: end; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--dev-border);">
                @csrf
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Value</label>
                    <input type="number" name="counter_value" class="dev-input" placeholder="10" style="padding: 0.4rem;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Suffix</label>
                    <input type="text" name="suffix" class="dev-input" value="+" style="padding: 0.4rem;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Label</label>
                    <input type="text" name="label" class="dev-input" placeholder="e.g. Clients" style="padding: 0.4rem;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Order</label>
                    <input type="number" name="sort_order" class="dev-input" value="{{ $heroStats->count() + 1 }}" style="padding: 0.4rem;" required>
                </div>
                <button type="submit" class="dev-btn" style="padding: 0.4rem 0.75rem; font-size: 0.75rem; background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid #10b981;">+ Add</button>
            </form>
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════
             ABOUT CARDS MANAGEMENT (inline under about section)
        ═══════════════════════════════════════════════ --}}
        @if($section->section_key === 'about')
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--dev-border);">
            <h4 style="margin-bottom: 1rem; color: var(--dev-accent);">🃏 About Cards</h4>

            @foreach($aboutCards as $card)
            <div style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem; align-items: end;">
                <form action="{{ route('developer.sections.about-cards.update', $card->id) }}" method="POST" style="display: grid; grid-template-columns: 80px 1fr 2fr 80px auto; gap: 0.75rem; align-items: end; flex: 1;">
                    @csrf @method('PUT')
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Icon</label>
                        <input type="text" name="icon" class="dev-input" value="{{ $card->icon }}" style="padding: 0.4rem; text-align: center;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Title</label>
                        <input type="text" name="title" class="dev-input" value="{{ $card->title }}" style="padding: 0.4rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Description</label>
                        <input type="text" name="description" class="dev-input" value="{{ $card->description }}" style="padding: 0.4rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Order</label>
                        <input type="number" name="sort_order" class="dev-input" value="{{ $card->sort_order }}" style="padding: 0.4rem;">
                    </div>
                    <button type="submit" class="dev-btn" style="padding: 0.4rem 0.75rem; font-size: 0.75rem;">Save</button>
                </form>
                <form action="{{ route('developer.sections.about-cards.destroy', $card->id) }}" method="POST" onsubmit="return confirm('Delete this card?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="dev-btn dev-btn-danger" style="padding: 0.4rem 0.75rem; font-size: 0.75rem;">×</button>
                </form>
            </div>
            @endforeach

            {{-- Add New Card --}}
            <form action="{{ route('developer.sections.about-cards.store') }}" method="POST" style="display: grid; grid-template-columns: 80px 1fr 2fr 80px auto; gap: 0.75rem; align-items: end; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--dev-border);">
                @csrf
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Icon</label>
                    <input type="text" name="icon" class="dev-input" placeholder="🚀" style="padding: 0.4rem; text-align: center;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Title</label>
                    <input type="text" name="title" class="dev-input" placeholder="Skill Name" style="padding: 0.4rem;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Description</label>
                    <input type="text" name="description" class="dev-input" placeholder="Short description..." style="padding: 0.4rem;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Order</label>
                    <input type="number" name="sort_order" class="dev-input" value="{{ $aboutCards->count() + 1 }}" style="padding: 0.4rem;" required>
                </div>
                <button type="submit" class="dev-btn" style="padding: 0.4rem 0.75rem; font-size: 0.75rem; background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid #10b981;">+ Add</button>
            </form>
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════
             SERVICE ITEMS MANAGEMENT (inline under services section)
        ═══════════════════════════════════════════════ --}}
        @if($section->section_key === 'services')
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--dev-border);">
            <h4 style="margin-bottom: 1rem; color: var(--dev-accent);">💼 Service Items</h4>

            @foreach($serviceItems as $item)
            <div style="display: flex; gap: 0.75rem; margin-bottom: 0.75rem; align-items: end;">
                <form action="{{ route('developer.sections.service-items.update', $item->id) }}" method="POST" style="display: grid; grid-template-columns: 80px 1fr 2fr 120px 80px auto; gap: 0.75rem; align-items: end; flex: 1;">
                    @csrf @method('PUT')
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Icon</label>
                        <input type="text" name="icon" class="dev-input" value="{{ $item->icon }}" style="padding: 0.4rem; text-align: center;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Title</label>
                        <input type="text" name="title" class="dev-input" value="{{ $item->title }}" style="padding: 0.4rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Description</label>
                        <input type="text" name="description" class="dev-input" value="{{ $item->description }}" style="padding: 0.4rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Price Text</label>
                        <input type="text" name="price_text" class="dev-input" value="{{ $item->price_text }}" style="padding: 0.4rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Order</label>
                        <input type="number" name="sort_order" class="dev-input" value="{{ $item->sort_order }}" style="padding: 0.4rem;">
                    </div>
                    <button type="submit" class="dev-btn" style="padding: 0.4rem 0.75rem; font-size: 0.75rem;">Save</button>
                </form>
                <form action="{{ route('developer.sections.service-items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this service?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="dev-btn dev-btn-danger" style="padding: 0.4rem 0.75rem; font-size: 0.75rem;">×</button>
                </form>
            </div>
            @endforeach

            {{-- Add New Service --}}
            <form action="{{ route('developer.sections.service-items.store') }}" method="POST" style="display: grid; grid-template-columns: 80px 1fr 2fr 120px 80px auto; gap: 0.75rem; align-items: end; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--dev-border);">
                @csrf
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Icon</label>
                    <input type="text" name="icon" class="dev-input" placeholder="💻" style="padding: 0.4rem; text-align: center;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Title</label>
                    <input type="text" name="title" class="dev-input" placeholder="Service Name" style="padding: 0.4rem;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Description</label>
                    <input type="text" name="description" class="dev-input" placeholder="Service description..." style="padding: 0.4rem;" required>
                </div>
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Price Text</label>
                    <input type="text" name="price_text" class="dev-input" placeholder="From IDR 5jt" style="padding: 0.4rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.7rem; color: var(--dev-text-muted); margin-bottom: 0.25rem;">Order</label>
                    <input type="number" name="sort_order" class="dev-input" value="{{ $serviceItems->count() + 1 }}" style="padding: 0.4rem;" required>
                </div>
                <button type="submit" class="dev-btn" style="padding: 0.4rem 0.75rem; font-size: 0.75rem; background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid #10b981;">+ Add</button>
            </form>
        </div>
        @endif

    </div>
    @endforeach
</div>
@endsection
