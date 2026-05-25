<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>REINTECH | Digital Universe Creator</title>
    <meta name="description" content="Full-Stack Developer & Tech Creator based in Indonesia. Crafting high-performance digital experiences.">
    {{-- Fonts loaded via app.css (Archivo + Space Grotesk) from MASTER.md design system --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- ================================================
     PRELOADER — SPACE REALM FANTASY
================================================ --}}
<div id="preloader">
    <canvas id="preloader-canvas"></canvas>
    <div id="preloader-text">
        <div id="preloader-title">REINTECH</div>
        <div id="preloader-sub">BEYOND THE DIMENSION</div>
    </div>
    <button id="enter-btn">ENTER UNIVERSE</button>
</div>

{{-- ================================================
     SIDEBAR — ICON TUBE (floating, 72px)
================================================ --}}
<aside class="sidebar" id="sidebar">

    <nav class="sidebar-nav">
        <a href="#hero" class="nav-link active" data-label="Home" id="nav-hero">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            <span class="nav-label">Home</span>
        </a>
        <a href="#about" class="nav-link" data-label="About" id="nav-about">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
            <span class="nav-label">About</span>
        </a>
        <a href="#projects" class="nav-link" data-label="Projects" id="nav-projects">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3l5 9H7l5-9z"/></svg>
            <span class="nav-label">Projects</span>
        </a>
        <a href="#services" class="nav-link" data-label="Services" id="nav-services">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
            <span class="nav-label">Services</span>
        </a>
        <a href="#contact" class="nav-link" data-label="Contact" id="nav-contact">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
            <span class="nav-label">Contact</span>
        </a>
    </nav>

    <div class="sidebar-divider"></div>

    <div class="sidebar-social">
        @foreach($socials as $social)
        <a href="{{ $social->url }}" target="_blank" class="social-link" data-label="{{ $social->label }}">
            {!! $social->icon_svg !!}
            <span class="nav-label">{{ $social->label }}</span>
        </a>
        @endforeach
    </div>
</aside>

{{-- ================================================
     FLOATING TOPBAR
================================================ --}}
<header class="topbar" id="topbar">
    <div class="topbar-left">
        <span class="topbar-status"></span>
        <span class="topbar-available">{{ $identity->topbar_status_text }}</span>
    </div>
    <div class="topbar-right">
        <span class="topbar-pill">💻 {{ $identity->topbar_role_text }}</span>
        <button class="theme-toggle" onclick="window.toggleTheme()" id="theme-toggle" aria-label="Toggle Theme">
            <span id="theme-icon">☀️</span>
        </button>
    </div>
</header>

{{-- ================================================
     MAIN CONTENT
================================================ --}}
<main class="main-content">

    @if(isset($sections['hero']) && $sections['hero']->is_visible)
    <section class="section hero" id="hero" data-section>
        <canvas class="section-canvas" id="hero-canvas"></canvas>
        <div class="hero-glow-1"></div>
        <div class="hero-glow-2"></div>
        <div class="hero-glow-3"></div>
        <div class="section-inner hero-inner">
            <div class="hero-badge">{{ $sections['hero']->subtitle }}</div>
            <h1 class="hero-title">
                {{ $sections['hero']->title }}
            </h1>
            <p class="hero-desc">
                {{ $sections['hero']->description }}
            </p>
            <div class="hero-cta">
                <a href="#projects" class="btn-primary">Explore My Work →</a>
                <a href="#contact" class="btn-ghost">Let's Talk</a>
            </div>
            <div class="hero-stats">
                @foreach($heroStats as $stat)
                @if(!$loop->first)<div class="stat-divider"></div>@endif
                <div class="stat-item">
                    <span class="stat-num counter" data-target="{{ $stat->counter_value }}">0</span><span class="stat-suffix">{{ $stat->suffix }}</span>
                    <span class="stat-label">{{ $stat->label }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 👤 ABOUT — DNA Canvas --}}
    @if(isset($sections['about']) && $sections['about']->is_visible)
    <section class="section" id="about" data-section>
        <canvas class="section-canvas" id="about-canvas"></canvas>
        <div class="section-inner">
            <div class="section-tag reveal">{{ $sections['about']->subtitle }}</div>
            <h2 class="section-title reveal">{{ $sections['about']->title }}</h2>
            <div style="margin-bottom: 2rem; color: var(--text-muted); max-width: 800px;" class="reveal">
                {{ $sections['about']->description }}
            </div>
            <div class="about-grid">
                @foreach($aboutCards as $card)
                <div class="about-card reveal">
                    <div class="about-icon">{{ $card->icon }}</div>
                    <h3>{{ $card->title }}</h3>
                    <p>{{ $card->description }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 🚀 PROJECTS — Matrix Rain Canvas --}}
    @if(isset($sections['projects']) && $sections['projects']->is_visible)
    <section class="section" id="projects" data-section>
        <canvas class="section-canvas" id="projects-canvas"></canvas>
        <div class="section-inner">
            <div class="section-tag reveal">{{ $sections['projects']->subtitle }}</div>
            <h2 class="section-title reveal">{{ $sections['projects']->title }}</h2>
            <div style="margin-bottom: 2rem; color: var(--text-muted); max-width: 800px;" class="reveal">
                {{ $sections['projects']->description }}
            </div>
            <div class="projects-grid">
                @foreach($projects as $project)
                <div class="project-card reveal" onclick="openProjectModal({{ $project->id }})">
                    <div class="project-thumb" style="background-image: url('{{ $project->main_image ? Storage::url($project->main_image) : '' }}'); background-size: cover; background-position: center;">
                        @if(!$project->main_image)
                        <div class="project-thumb-icon">📁</div>
                        @endif
                        <div class="project-thumb-glow" style="background: radial-gradient(circle, rgba(124,92,252,0.3), transparent 70%)"></div>
                    </div>
                    <div class="project-info">
                        <div class="project-meta">
                            <span class="project-year">{{ $project->year }}</span>
                            <span class="project-type">{{ $project->category }}</span>
                        </div>
                        <h3 class="project-title">{{ $project->title }}</h3>
                        <p class="project-desc">{{ $project->short_description }}</p>
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
                        'images' => $project->images->map(fn($img) => Storage::url($img->image_path))
                    ]) !!}
                </template>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 💼 SERVICES — Circuit Canvas --}}
    @if(isset($sections['services']) && $sections['services']->is_visible)
    <section class="section" id="services" data-section>
        <canvas class="section-canvas" id="services-canvas"></canvas>
        <div class="section-inner">
            <div class="section-tag reveal">{{ $sections['services']->subtitle }}</div>
            <h2 class="section-title reveal">{{ $sections['services']->title }}</h2>
            <div style="margin-bottom: 2rem; color: var(--text-muted); max-width: 800px;" class="reveal">
                {{ $sections['services']->description }}
            </div>
            <div class="services-grid">
                @foreach($serviceItems as $item)
                <div class="service-card reveal">
                    <div class="service-icon">{{ $item->icon }}</div>
                    <h3 class="service-title">{{ $item->title }}</h3>
                    <p class="service-desc">{{ $item->description }}</p>
                    @if($item->price_text)
                    <div class="service-price">{{ $item->price_text }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ✉️ CONTACT — Wormhole Canvas --}}
    @if(isset($sections['contact']) && $sections['contact']->is_visible)
    <section class="section" id="contact" data-section>
        <canvas class="section-canvas" id="contact-canvas"></canvas>
        <div class="section-inner">
            <div class="section-tag reveal">{{ $sections['contact']->subtitle }}</div>
            <h2 class="section-title reveal">{{ $sections['contact']->title }}</h2>
            <div class="contact-wrapper reveal">
                <p class="contact-sub">{{ $sections['contact']->description }}</p>
                <a href="mailto:{{ $identity->contact_email }}" class="contact-email">{{ $identity->contact_email }}</a>
                <div class="contact-cta">
                    <a href="mailto:{{ $identity->contact_email }}" class="btn-primary">Send Email →</a>
                    <a href="https://wa.me/{{ $identity->contact_whatsapp }}" target="_blank" class="btn-ghost">WhatsApp</a>
                </div>
            </div>
        </div>
    </section>
    @endif

    <footer class="footer">
        <span>© {{ date('Y') }} {{ $identity->logo_subtext }} — {{ $identity->footer_text }}</span>
        <span class="footer-brand">{{ $identity->logo_subtext }}</span>
    </footer>

</main>

{{-- FLOATING LOGO BOTTOM RIGHT --}}
@if($identity->sidebar_icon_type === 'image' && $identity->sidebar_icon_value)
    <div class="floating-logo" id="floating-logo" style="background-image: url('{{ Storage::url($identity->sidebar_icon_value) }}'); background-size: cover; background-position: center; color: transparent;"></div>
@else
    <div class="floating-logo" id="floating-logo">{{ $identity->sidebar_icon_value }}</div>
@endif

{{-- MOBILE BOTTOM TAB BAR --}}
<nav class="mobile-tab-bar">
    <a href="#hero" class="tab-item active">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        <span>Home</span>
    </a>
    <a href="#about" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
        <span>About</span>
    </a>
    <a href="#projects" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3l5 9H7l5-9z"/></svg>
        <span>Projects</span>
    </a>
    <a href="#services" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
        <span>Services</span>
    </a>
    <a href="#contact" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
        <span>Contact</span>
    </a>
</nav>

{{-- PROJECT MODAL OVERLAY --}}
<div id="project-modal" class="project-modal">
    <div class="project-modal-backdrop" onclick="closeProjectModal()"></div>
    <div class="project-modal-content">
        <button class="project-modal-close" onclick="closeProjectModal()">×</button>
        <div class="project-modal-body">
            <h2 id="modal-title" style="margin-bottom: 0.5rem; font-size: 2rem;"></h2>
            <div style="color: var(--text-muted); margin-bottom: 1rem;">
                <span id="modal-year"></span> • <span id="modal-category"></span>
            </div>
            
            <div id="modal-tags" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2rem;"></div>
            
            <div id="modal-desc" style="line-height: 1.6; margin-bottom: 2rem;"></div>
            
            <div style="display: flex; gap: 1rem; margin-bottom: 3rem;">
                <a id="modal-live" href="#" target="_blank" class="btn-primary" style="display: none;">Live Site</a>
                <a id="modal-repo" href="#" target="_blank" class="btn-ghost" style="display: none;">Repository</a>
            </div>

            <h3 style="margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">Gallery</h3>
            <div id="modal-gallery" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;"></div>
        </div>
    </div>
</div>

<style>
    .project-modal { position: fixed; inset: 0; z-index: 99999; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; }
    .project-modal.active { display: flex; opacity: 1; }
    .project-modal-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); }
    .project-modal-content { position: relative; background: var(--bg); width: 90%; max-width: 900px; max-height: 90vh; border-radius: 12px; border: 1px solid var(--border); overflow-y: auto; z-index: 1; transform: translateY(20px); transition: transform 0.3s; }
    .project-modal.active .project-modal-content { transform: translateY(0); }
    .project-modal-close { position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.1); border: none; color: var(--text); width: 32px; height: 32px; border-radius: 50%; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .project-modal-body { padding: 3rem 2rem; }
</style>

<script>
    function openProjectModal(id) {
        const dataStr = document.getElementById('project-data-' + id).innerHTML;
        const data = JSON.parse(dataStr);
        
        document.getElementById('modal-title').innerText = data.title;
        document.getElementById('modal-year').innerText = data.year || '';
        document.getElementById('modal-category').innerText = data.category || '';
        document.getElementById('modal-desc').innerHTML = data.long_description ? data.long_description.replace(/\n/g, '<br>') : '';
        
        const tagsContainer = document.getElementById('modal-tags');
        tagsContainer.innerHTML = '';
        if (data.tech_stack) {
            data.tech_stack.forEach(tech => {
                const span = document.createElement('span');
                span.className = 'project-tag';
                span.innerText = tech;
                tagsContainer.appendChild(span);
            });
        }

        const liveBtn = document.getElementById('modal-live');
        if (data.live_url) {
            liveBtn.href = data.live_url;
            liveBtn.style.display = 'inline-block';
        } else {
            liveBtn.style.display = 'none';
        }

        const repoBtn = document.getElementById('modal-repo');
        if (data.repo_url) {
            repoBtn.href = data.repo_url;
            repoBtn.style.display = 'inline-block';
        } else {
            repoBtn.style.display = 'none';
        }

        const galleryContainer = document.getElementById('modal-gallery');
        galleryContainer.innerHTML = '';
        if (data.images && data.images.length > 0) {
            data.images.forEach(imgUrl => {
                const img = document.createElement('img');
                img.src = imgUrl;
                img.style.width = '100%';
                img.style.borderRadius = '8px';
                img.style.border = '1px solid var(--border)';
                galleryContainer.appendChild(img);
            });
        } else {
            galleryContainer.innerHTML = '<p style="color: var(--text-muted);">No gallery images available.</p>';
        }

        document.body.style.overflow = 'hidden';
        document.getElementById('project-modal').classList.add('active');
    }

    function closeProjectModal() {
        document.getElementById('project-modal').classList.remove('active');
        document.body.style.overflow = '';
    }
</script>

</body>
</html>