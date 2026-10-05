<!DOCTYPE html>
<html lang="id" data-dev-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') — {{ \App\Support\AdminContent::admin()['name'] }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/reintech-logo.svg') }}">

    {{-- Tema dipasang SEBELUM CSS termuat supaya tidak berkedip terang lalu
         gelap saat halaman dibuka. Dibungkus try: localStorage bisa diblokir. --}}
    <script>
        try {
            document.documentElement.setAttribute('data-dev-theme', localStorage.getItem('dev-theme') || 'dark');
        } catch (e) {}
    </script>

    @vite(['resources/css/developer.css', 'resources/js/developer.js'])

    {{-- Aset KHUSUS halaman. Yang global hanya developer.css + developer.js;
         sisanya didorong sendiri oleh halaman yang memakainya lewat
         @push('page-styles') / @push('page-scripts'). --}}
    @stack('page-styles')

    {{-- Animasi pembuka hanya dimuat pada kunjungan pertama setelah login
         (?welcome=1). Kunjungan lain tidak mengunduh satu byte pun darinya. --}}
    {{-- Panel ini juga bisa tampil di dalam bingkai pratinjau (Tampilan Admin). --}}
    @if (request()->boolean('pf_preview'))
        @vite(['resources/js/preview-receiver.js'])
    @endif

    @if (request()->boolean('welcome'))
        @vite(['resources/css/reveal.css', 'resources/js/reveal.js'])
    @endif
</head>
<body>
@if (request()->boolean('welcome'))
    {{-- Dirender di server agar dashboard tidak sempat berkedip terlihat
         sebelum overlay menutupinya. --}}
    <div id="app-reveal" aria-hidden="true"></div>
@endif
@php
    // Ikon garis gaya Lucide, 24x24. Ditulis sekali di sini, bukan disebar ke
    // setiap tautan, supaya menu tetap mudah dibaca.
    $ikon = [
        'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'identity'  => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'socials'   => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>',
        'sections'  => '<path d="M3 5h18M3 12h18M3 19h12"/>',
        'projects'  => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/>',
        'messages'  => '<path d="M4 4h16v12H7l-3 3Z"/>',
        'settings'  => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1Z"/>',
        'career'    => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 13h18"/>',
        'stack'     => '<path d="m12 3 9 5-9 5-9-5Z"/><path d="m3 13 9 5 9-5"/>',
        'chrome'    => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 4v5"/>',
        'export'    => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9Z"/><path d="M14 3v6h6M12 18v-6M9 15l3 3 3-3"/>',
        'site'      => '<path d="M14 3h7v7M10 14 21 3M19 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5"/>',
        'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
    ];
    $svg = fn (string $k) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $ikon[$k] . '</svg>';

    $menu = [
        ['Utama', [
            ['admin.dashboard', 'admin.dashboard', 'Dashboard', 'dashboard'],
        ]],
        ['Konten', [
            ['admin.identity', 'admin.identity', 'Identitas & SEO', 'identity'],
            ['admin.identity.socials', 'admin.identity.socials', 'Media Sosial', 'socials'],
            ['admin.sections', 'admin.sections', 'Bagian Halaman', 'sections'],
            ['admin.projects', 'admin.projects*', 'Proyek', 'projects'],
            ['admin.experiences', 'admin.experiences*', 'Pengalaman', 'career'],
            ['admin.tech-stacks', 'admin.tech-stacks*', 'Tech Stack', 'stack'],
        ]],
        ['Sistem', [
            ['admin.content', 'admin.content*', 'Tampilan Admin', 'chrome'],
            ['admin.export', 'admin.export*', 'Ekspor PDF', 'export'],
            ['admin.settings', 'admin.settings', 'Pengaturan', 'settings'],
        ]],
    ];

    $chrome = \App\Support\AdminContent::admin();
    $sosial = $chrome['show_socials']
        ? \App\Models\SocialLink::visible()->ordered()->get(['platform', 'label', 'url'])
        : collect();

    $nama = session('developer_name', 'Admin');
    $inisial = collect(explode(' ', $nama))->filter()->take(2)->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))->implode('');
@endphp

<div class="dev-layout" id="dev-layout">
    <aside class="dev-sidebar" id="dev-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="dev-brand">
            <img src="{{ asset('images/reintech-logo.svg') }}" alt="" width="38" height="38">
            <div>
                <strong data-pf="admin_brand_name">{{ $chrome['name'] }}</strong>
                <span data-pf="admin_brand_tagline">{{ $chrome['tagline'] }}</span>
            </div>
        </a>

        <nav class="dev-nav" aria-label="Menu admin">
            @foreach ($menu as [$kelompok, $tautan])
                <div class="dev-nav-label">{{ $kelompok }}</div>
                @foreach ($tautan as [$rute, $pola, $label, $k])
                    <a href="{{ route($rute) }}"
                       class="dev-nav-link {{ request()->routeIs($pola) ? 'active' : '' }}"
                       @if (request()->routeIs($pola)) aria-current="page" @endif>
                        {!! $svg($k) !!}
                        <span>{{ $label }}</span>
                    </a>
                @endforeach
            @endforeach

            <div class="dev-nav-label">Situs</div>
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="dev-nav-link">
                {!! $svg('site') !!}
                <span>Lihat Portofolio</span>
            </a>
        </nav>

        <div class="dev-sidebar-foot">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="dev-nav-link dev-logout">
                    {!! $svg('logout') !!}
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="dev-content">
        <header class="dev-topbar">
            <div style="display:flex; align-items:center; gap:.75rem;">
                <button type="button" class="dev-icon-btn dev-menu-toggle" id="dev-menu-toggle" aria-label="Buka menu" aria-controls="dev-sidebar" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1>@yield('title', 'Dashboard')</h1>
            </div>

            <div class="dev-topbar-right">
                <button type="button" class="dev-icon-btn" id="theme-toggle" aria-label="Ganti tema">
                    {{-- Ikon diganti developer.js sesuai tema aktif. --}}
                </button>
                <div class="dev-user">
                    <div class="dev-avatar" aria-hidden="true">{{ $inisial ?: 'A' }}</div>
                    <span data-pf="akun.name">{{ $nama }}</span>
                </div>
            </div>
        </header>

        <main class="dev-main">
            @if (session('success'))
                <div class="alert alert-success" role="status">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
                    <ul style="margin:0; padding-left:1.1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="dev-footer">
            <span>&copy; {{ now()->year }}
                @if ($chrome['footer_link'])
                    <a href="{{ $chrome['footer_link'] }}" target="_blank" rel="noopener" data-pf="admin_footer_text">{{ $chrome['footer_name'] }}</a>
                @else
                    <span data-pf="admin_footer_text">{{ $chrome['footer_name'] }}</span>
                @endif
            </span>
            @if ($sosial->isNotEmpty())
                <nav class="dev-footer-social" aria-label="Media sosial">
                    @foreach ($sosial as $s)
                        <a href="{{ $s->url }}" target="_blank" rel="noopener" aria-label="{{ $s->label ?: $s->nama_platform }}" title="{{ $s->label ?: $s->nama_platform }}">{!! $s->ikon !!}</a>
                    @endforeach
                </nav>
            @endif
        </footer>
    </div>
</div>

@stack('page-scripts')
</body>
</html>
