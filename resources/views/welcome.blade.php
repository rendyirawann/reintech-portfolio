<!DOCTYPE html>
@php
    // Mode pratinjau: halaman dibuka di dalam iframe panel admin. Kelasnya
    // dipasang di SERVER, sebelum cat pertama, supaya preloader dan animasi
    // masuk tidak sempat tampil sama sekali di dalam pratinjau.
    $pratinjau = request()->boolean('pf_preview');
@endphp
<html lang="id" data-theme="dark" @class(['is-preview' => $pratinjau])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.seo')
    {{-- Fonts loaded via app.css (Archivo + Space Grotesk) from MASTER.md design system --}}
    @vite(['resources/css/app.css', 'resources/css/front.css', 'resources/js/app.js', 'resources/js/front.js'])

    @if ($pratinjau)
        {{-- Hanya di dalam pratinjau admin: tampilkan semuanya seketika —
             tanpa preloader, tanpa animasi gulir — supaya yang disunting
             langsung terlihat. !important perlu karena GSAP menulis opacity
             sebagai gaya inline. Pengunjung biasa tidak pernah memuat ini. --}}
        <style>
            .is-preview #preloader { display: none !important; }
            .is-preview .reveal,
            .is-preview .hero-badge,
            .is-preview .hero-title,
            .is-preview .hero-desc,
            .is-preview .hero-cta,
            .is-preview .hero-stats { opacity: 1 !important; transform: none !important; }
            .pf-flash { animation: pf-flash 900ms ease-out; }
            @keyframes pf-flash {
                0%   { outline: 2px solid rgba(34, 211, 238, .9); outline-offset: 3px; background-color: rgba(34, 211, 238, .12); }
                100% { outline: 2px solid rgba(34, 211, 238, 0); outline-offset: 6px; background-color: transparent; }
            }
        </style>
        @vite(['resources/js/preview-receiver.js'])
    @endif
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
        <a href="#experience" class="nav-link" data-label="Experience" id="nav-experience">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 13h18"/></svg>
            <span class="nav-label">Experience</span>
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
            {{-- Ikon dirakit dari platform hasil pengenalan URL (App\Support\Platform),
                 bukan dari markup yang diketik pengelola. --}}
            {!! $social->ikon !!}
            <span class="nav-label" data-pf="socials.{{ $social->id }}.label">{{ $social->label }}</span>
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
        <span class="topbar-available" data-pf="identity.topbar_status_text">{{ $identity->topbar_status_text }}</span>
    </div>
    <div class="topbar-right">
        <span class="topbar-pill"><svg class="topbar-pill__ikon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m8 9-4 3 4 3M16 9l4 3-4 3M13.5 6l-3 12"/></svg><span data-pf="identity.topbar_role_text">{{ $identity->topbar_role_text }}</span></span>
        <button class="theme-toggle" onclick="window.toggleTheme()" id="theme-toggle" aria-label="Toggle Theme">
            <svg class="ikon-bulan" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>
            <svg class="ikon-matahari" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
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
            <div class="hero-badge" data-pf="sections.hero.subtitle">{{ $sections['hero']->subtitle }}</div>
            <h1 class="hero-title">
                <span data-pf="sections.hero.title">{{ $sections['hero']->title }}</span>
            </h1>
            <p class="hero-desc">
                <span data-pf="sections.hero.description">{{ $sections['hero']->description }}</span>
            </p>
            <div class="hero-cta">
                <a href="#projects" class="btn-primary">Explore My Work →</a>
                <a href="#contact" class="btn-ghost">Let's Talk</a>
            </div>
            <div class="hero-stats">
                @foreach($heroStats as $stat)
                @if(!$loop->first)<div class="stat-divider"></div>@endif
                <div class="stat-item">
                    <span class="stat-num counter" data-target="{{ $stat->nilai($projects) }}" @unless ($stat->auto_key) data-pf="hero_stats.{{ $stat->id }}.counter_value" @endunless>0</span><span class="stat-suffix" data-pf="hero_stats.{{ $stat->id }}.suffix">{{ $stat->suffix }}</span>
                    <span class="stat-label" data-pf="hero_stats.{{ $stat->id }}.label">{{ $stat->label }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- TECH STACK — pita ikon berjalan. Daftarnya dikelola di admin › Tech Stack. --}}
    @if ($techStacks->isNotEmpty())
    <section class="stack-band" id="stack" aria-label="Tech stack">
        <p class="stack-band__label">Tech stack yang saya pakai</p>
        @foreach ([$techStacks, $techStacks->reverse()] as $b => $baris)
            <div class="stack-marquee {{ $b ? 'stack-marquee--balik' : '' }}">
                {{-- Isi digandakan agar putarannya tanpa sambungan; salinan kedua disembunyikan dari pembaca layar. --}}
                @foreach ([false, true] as $salinan)
                    <ul class="stack-marquee__track" @if ($salinan) aria-hidden="true" @endif>
                        @foreach ($baris as $t)
                            {{-- Ikon lewat CSS mask dari berkas lokal: satu unduhan ter-cache, bukan path SVG berulang di HTML. --}}
                            <li class="stack-chip" style="--brand: {{ $t->color ?: '#22d3ee' }}@if ($t->ikon_url); --ikon: url('{{ $t->ikon_url }}')@endif">
                                @if ($t->ikon_url)
                                    <i class="stack-chip__ikon" aria-hidden="true"></i>
                                @else
                                    <span class="stack-chip__mono" aria-hidden="true">{{ mb_substr($t->name, 0, 2) }}</span>
                                @endif
                                <span>{{ $t->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        @endforeach
    </section>
    @endif

    {{-- 👤 ABOUT — DNA Canvas --}}
    @if(isset($sections['about']) && $sections['about']->is_visible)
    <section class="section" id="about" data-section>
        <canvas class="section-canvas" id="about-canvas"></canvas>
        <div class="section-inner">
            <div class="section-tag reveal" data-pf="sections.about.subtitle">{{ $sections['about']->subtitle }}</div>
            <h2 class="section-title reveal" data-pf="sections.about.title">{{ $sections['about']->title }}</h2>
            <div style="margin-bottom: 2rem; color: var(--text-muted); max-width: 800px;" class="reveal">
                <span data-pf="sections.about.description">{{ $sections['about']->description }}</span>
            </div>
            <div class="profil reveal">
                <div class="profil__foto">
                    @if ($identity->profile_image)
                        <img src="{{ Storage::url($identity->profile_image) }}" alt="{{ $identity->full_name }}" width="320" height="400" loading="lazy" data-pf-img="identity.profile_image">
                    @else
                        <span class="profil__inisial" aria-hidden="true">{{ $identity->logo_text }}</span>
                    @endif
                </div>
                <div class="profil__isi">
                    <h3 class="profil__nama" data-pf="identity.full_name">{{ $identity->full_name }}</h3>
                    <p class="profil__headline" data-pf="identity.headline">{{ $identity->headline }}</p>
                    @foreach (preg_split('/\R+/', trim((string) $identity->summary)) as $paragraf)
                        @if ($paragraf !== '')<p class="profil__ringkas">{{ $paragraf }}</p>@endif
                    @endforeach
                    <dl class="profil__fakta">
                        @if ($identity->location)<div><dt>Lokasi</dt><dd data-pf="identity.location">{{ $identity->location }}</dd></div>@endif
                        @if ($identity->contact_email)<div><dt>Email</dt><dd><a href="mailto:{{ $identity->contact_email }}">{{ $identity->contact_email }}</a></dd></div>@endif
                        @if ($identity->languages)<div><dt>Bahasa</dt><dd>{{ $identity->languages }}</dd></div>@endif
                        @if ($pengalamanSejak)<div><dt>Berkarya sejak</dt><dd>{{ $pengalamanSejak }}</dd></div>@endif
                    </dl>
                </div>
            </div>

            <div class="about-grid">
                @foreach($aboutCards as $card)
                <div class="about-card reveal">
                    <div class="about-icon" data-pf="about_cards.{{ $card->id }}.icon">{{ $card->icon }}</div>
                    <h3 data-pf="about_cards.{{ $card->id }}.title">{{ $card->title }}</h3>
                    <p data-pf="about_cards.{{ $card->id }}.description">{{ $card->description }}</p>
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
            <div class="section-tag reveal" data-pf="sections.projects.subtitle">{{ $sections['projects']->subtitle }}</div>
            <h2 class="section-title reveal" data-pf="sections.projects.title">{{ $sections['projects']->title }}</h2>
            <div style="margin-bottom: 2rem; color: var(--text-muted); max-width: 800px;" class="reveal">
                <span data-pf="sections.projects.description">{{ $sections['projects']->description }}</span>
            </div>
            <form class="proyek-cari reveal" id="proyek-cari" action="{{ route('projects.list') }}" role="search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <label for="proyek-q" class="sr-only">Cari proyek</label>
                <input type="search" id="proyek-q" name="q" placeholder="Cari proyek, kategori, atau teknologi…" maxlength="80" autocomplete="off">
                <span class="proyek-cari__jumlah" id="proyek-jumlah" aria-live="polite">{{ $daftarProyek->total() }} proyek</span>
            </form>
            {{-- Halaman pertama dirender server (SEO, tanpa JS); halaman lain & pencarian lewat AJAX. --}}
            <div class="projects-grid" id="projects-grid" data-endpoint="{{ route('projects.list') }}">
                @foreach ($daftarProyek as $project)
                    @include('partials.project-card')
                @endforeach
                @if ($daftarProyek->isEmpty())<p class="proyek-kosong">Belum ada proyek.</p>@endif
            </div>
            <nav class="proyek-pager" id="proyek-pager" aria-label="Halaman proyek">
                @include('partials.project-pager', ['p' => $daftarProyek])
            </nav>
        </div>
    </section>
    @endif

    {{-- EXPERIENCE — timeline kerja & pendidikan (admin › Pengalaman) --}}
    @if ($experiences->isNotEmpty())
    <section class="section section--vanta" id="experience" data-section>
        <div class="kontur" aria-hidden="true"><i></i><i></i><i></i></div>
        <div class="section-inner">
            <div class="section-tag reveal">Perjalanan</div>
            <h2 class="section-title reveal">Pengalaman</h2>
            <ol class="timeline">
                @foreach ($experiences as $x)
                    <li class="timeline__item reveal timeline__item--{{ $x->type }}">
                        <span class="timeline__titik" aria-hidden="true"></span>
                        <div class="timeline__kartu">
                            <div class="timeline__atas">
                                <span class="timeline__jenis">{{ \App\Models\Experience::JENIS[$x->type] ?? $x->type }}</span>
                                <span class="timeline__periode">{{ $x->periode }} · {{ $x->durasi }}</span>
                            </div>
                            <h3 class="timeline__judul">{{ $x->title }}</h3>
                            <p class="timeline__org">{{ $x->organization }}@if ($x->employment_type) <span>· {{ $x->employment_type }}</span>@endif @if ($x->location)<span>· {{ $x->location }}</span>@endif</p>
                            @if ($x->grade)<p class="timeline__nilai">IPK <strong>{{ $x->grade }}</strong></p>@endif
                            @if ($x->link_url)
                                <a class="timeline__tautan" href="{{ $x->link_url }}" target="_blank" rel="noopener">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5v14Z"/><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"/></svg>
                                    <span>{{ $x->link_label ?: 'Lihat tautan' }}</span>
                                </a>
                            @endif
                            @if ($x->poin)
                                <ul class="timeline__poin">
                                    @foreach ($x->poin as $poin)<li>{{ $poin }}</li>@endforeach
                                </ul>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
    @endif

    {{-- 💼 SERVICES — Circuit Canvas --}}
    @if(isset($sections['services']) && $sections['services']->is_visible)
    <section class="section" id="services" data-section>
        <canvas class="section-canvas" id="services-canvas"></canvas>
        <div class="section-inner">
            <div class="section-tag reveal" data-pf="sections.services.subtitle">{{ $sections['services']->subtitle }}</div>
            <h2 class="section-title reveal" data-pf="sections.services.title">{{ $sections['services']->title }}</h2>
            <div style="margin-bottom: 2rem; color: var(--text-muted); max-width: 800px;" class="reveal">
                <span data-pf="sections.services.description">{{ $sections['services']->description }}</span>
            </div>
            <div class="services-grid">
                @foreach($serviceItems as $item)
                <div class="service-card reveal">
                    <div class="service-icon" data-pf="service_items.{{ $item->id }}.icon">{{ $item->icon }}</div>
                    <h3 class="service-title" data-pf="service_items.{{ $item->id }}.title">{{ $item->title }}</h3>
                    <p class="service-desc" data-pf="service_items.{{ $item->id }}.description">{{ $item->description }}</p>
                    @if($item->price_text)
                    <div class="service-price" data-pf="service_items.{{ $item->id }}.price_text">{{ $item->price_text }}</div>
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
            <div class="section-tag reveal" data-pf="sections.contact.subtitle">{{ $sections['contact']->subtitle }}</div>
            <h2 class="section-title reveal" data-pf="sections.contact.title">{{ $sections['contact']->title }}</h2>
            <div class="contact-wrapper reveal">
                <p class="contact-sub" data-pf="sections.contact.description">{{ $sections['contact']->description }}</p>
                <a href="mailto:{{ $identity->contact_email }}" class="contact-email" data-pf="identity.contact_email">{{ $identity->contact_email }}</a>
                <div class="contact-cta">
                    <a href="mailto:{{ $identity->contact_email }}" class="btn-primary">Send Email →</a>
                    @if ($identity->wa)
                        <a href="https://wa.me/{{ $identity->wa }}" target="_blank" rel="noopener" class="btn-ghost">WhatsApp</a>
                    @endif
                    @if ($identity->contact_linkedin)
                        <a href="{{ $identity->contact_linkedin }}" target="_blank" rel="noopener" class="btn-ghost">LinkedIn</a>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif

    <footer class="footer">
        <span>© {{ date('Y') }} <span data-pf="identity.logo_subtext">{{ $identity->logo_subtext }}</span> — <span data-pf="identity.footer_text">{{ $identity->footer_text }}</span></span>
        <span class="footer-brand" data-pf="identity.logo_subtext">{{ $identity->logo_subtext }}</span>
    </footer>

</main>

{{-- FLOATING LOGO — membuka menu layar penuh --}}
@php $gambarLogo = $identity->sidebar_icon_type === 'image' && $identity->sidebar_icon_value; @endphp
<button type="button" class="floating-logo" id="floating-logo" aria-controls="menu-layar" aria-expanded="false" aria-label="Buka menu"
    @if ($gambarLogo) style="background-image: url('{{ Storage::url($identity->sidebar_icon_value) }}'); background-size: cover; background-position: center; color: transparent;" @endif>
    <span class="floating-logo__teks" @unless ($gambarLogo) data-pf="identity.sidebar_icon_value_text" @endunless>{{ $gambarLogo ? '' : $identity->sidebar_icon_value }}</span>
    <span class="floating-logo__tutup" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg></span>
</button>

<nav class="menu-layar" id="menu-layar" aria-label="Menu utama" hidden>
    <div class="menu-layar__isi">
        <ol class="menu-layar__tautan">
            @foreach ([['#hero','Home'],['#about','About'],['#projects','Projects'],['#experience','Experience'],['#services','Services'],['#contact','Contact']] as $i => [$href, $label])
                <li><a href="{{ $href }}"><small>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</small>{{ $label }}</a></li>
            @endforeach
        </ol>
        <div class="menu-layar__samping">
            <label class="menu-cari">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <span class="sr-only">Cari proyek</span>
                <input type="search" id="menu-cari" placeholder="Cari proyek" maxlength="80" autocomplete="off">
            </label>
            <ul class="menu-cari__hasil" id="menu-cari-hasil"></ul>
            <a href="#contact" class="btn-primary menu-layar__cta">Hire Me <span aria-hidden="true">→</span></a>
            <div class="menu-layar__sosial">
                @foreach ($socials as $social)
                    <a href="{{ $social->url }}" target="_blank" rel="noopener" aria-label="{{ $social->label }}" title="{{ $social->label }}">{!! $social->ikon !!}</a>
                @endforeach
            </div>
        </div>
    </div>
</nav>

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
    <a href="#experience" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 13h18"/></svg>
        <span>Karier</span>
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
            <img id="modal-cover" alt="" style="display:none; width:100%; max-height:340px; object-fit:cover; border-radius:10px; margin-bottom:1.5rem; border:1px solid var(--border);">
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
        tampilkanProyek(JSON.parse(document.getElementById('project-data-' + id).innerHTML));
    }

    // Juga dipanggil penerima pratinjau admin dengan draf yang belum disimpan
    // (lihat preview-receiver.js), jadi semua isi dipasang sebagai teks.
    function tampilkanProyek(data) {
        const cover = document.getElementById('modal-cover');
        cover.style.display = data.main_image ? 'block' : 'none';
        if (data.main_image) cover.src = data.main_image;

        document.getElementById('modal-title').innerText = data.title;
        document.getElementById('modal-year').innerText = data.year || '';
        document.getElementById('modal-category').innerText = data.category || '';
        // textContent + pre-line: baris baru tetap tampil tanpa menafsirkan markup.
        const desc = document.getElementById('modal-desc');
        desc.style.whiteSpace = 'pre-line';
        desc.textContent = data.long_description || '';
        
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
            galleryContainer.innerHTML = '<p style="color: var(--text-muted);">Belum ada gambar galeri.</p>';
        }

        document.body.style.overflow = 'hidden';
        document.getElementById('project-modal').classList.add('active');
    }

    window.pfProyek = tampilkanProyek;

    function closeProjectModal() {
        document.getElementById('project-modal').classList.remove('active');
        document.body.style.overflow = '';
    }
</script>

</body>
</html>