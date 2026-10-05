@php
    // Semua teks halaman ini disunting di Tampilan Admin › Halaman Login & Loader.
    $l = \App\Support\AdminContent::login();
    $logo = asset('images/reintech-logo.svg');
    $galatPertama = $errors->first();
    // Pratinjau: halaman ini dirender di dalam panel admin (lihat
    // DeveloperContentController::loginPreview). Selain itu selalu false.
    $pratinjau = $pratinjau ?? false;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Halaman login tidak boleh muncul di hasil pencarian. --}}
    <meta name="robots" content="noindex, nofollow">
    <title>Masuk — {{ $l['name'] }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ $logo }}">

    <script>
        // Waktu preloader mulai tampil, supaya auth.js menahannya tepat 1,2
        // detik berapa pun cepatnya halaman dimuat.
        window.__authPreloadStart = Date.now();

        // Tema terang/gelap dipasang sebelum cat pertama. Kuncinya SAMA dengan
        // panel admin, jadi pilihan di satu tempat berlaku di keduanya.
        try {
            document.documentElement.setAttribute('data-dev-theme', localStorage.getItem('dev-theme') || 'dark');
        } catch (e) {}

        // Penahan click-jacking untuk peramban yang mengabaikan X-Frame-Options.
        // Satu-satunya bingkai yang diizinkan adalah pratinjau se-origin milik
        // panel admin; header tetap melarang origin lain.
        if (window.top !== window.self && !@json($pratinjau)) {
            window.top.location.replace(window.self.location.href);
        }
    </script>

    @vite(['resources/css/auth.css', 'resources/js/auth.js'])
    @if ($pratinjau)
        @vite(['resources/js/preview-receiver.js'])
    @endif
</head>
<body class="auth-body">
    <button type="button" class="auth-theme-toggle" id="auth-theme-toggle" aria-label="Ganti tema terang / gelap">
        <svg data-when="dark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>
        <svg data-when="light" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
    </button>

    {{-- Preloader masuk: dirender di server agar tampil sejak cat pertama.
         auth.js memudarkannya setelah 1,2 detik; CSS membawa waktu yang
         sama sebagai cadangan bila JavaScript tidak jalan. --}}
    <div id="auth-preloader" role="status" aria-live="polite">
        <div class="auth-preloader__mark">
            <span class="auth-preloader__ring" aria-hidden="true"></span>
            <span class="auth-preloader__ring auth-preloader__ring--alt" aria-hidden="true"></span>
            <img src="{{ $logo }}" alt="" width="52" height="52">
        </div>
        <div class="auth-preloader__label" data-pf="login_loader_label">{{ $l['loader_label'] }}</div>
        <div class="auth-preloader__bar" aria-hidden="true"><span></span></div>
        <div class="auth-preloader__text" data-pf="login_loader_text">{{ $l['loader_text'] }}</div>
    </div>

    <div class="auth-shell">
        <div class="auth-bg" aria-hidden="true"></div>
        <div id="auth-vanta" aria-hidden="true" data-color="#22d3ee" data-bg="#05070d" data-color-light="#0891b2" data-bg-light="#f4f7fb"></div>

        {{-- Panel cerita --}}
        <section class="auth-story">
            <a href="{{ route('home') }}" class="auth-story__brand">
                <img src="{{ $logo }}" alt="" class="auth-story__logo" width="52" height="52">
                <span>
                    <span class="auth-story__name" data-pf="login_brand_name">{{ $l['name'] }}</span>
                    <span class="auth-story__tagline" data-pf="login_tagline">{{ $l['tagline'] }}</span>
                </span>
            </a>

            <div>
                <p class="auth-story__kicker" data-pf="login_kicker">{{ $l['kicker'] }}</p>
                <h1 class="auth-story__headline"><span class="auth-story__h1" data-pf="login_headline_1">{{ $l['headline_1'] }}</span><span data-pf="login_headline_2">{{ $l['headline_2'] }}</span></h1>
                <p class="auth-story__lead" data-pf="login_lead">{{ $l['lead'] }}</p>

                <ul class="auth-story__points">
                    @foreach ($l['points'] as $i => $poin)
                        <li>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            <span data-pf="login_point_{{ $i + 1 }}">{{ $poin }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="auth-story__foot">&copy; {{ now()->year }} <span data-pf="login_brand_name">{{ $l['name'] }}</span> &middot; <span data-pf="login_tagline">{{ $l['tagline'] }}</span></p>
        </section>

        {{-- Panel formulir --}}
        <main class="auth-panel">
            <div class="auth-card">

                {{-- Blok merek — hanya tampil saat panel cerita disembunyikan (ponsel). --}}
                <div class="auth-card__brand">
                    <img src="{{ $logo }}" alt="" width="56" height="56">
                    <span data-pf="login_brand_name">{{ $l['name'] }}</span>
                </div>

                <h2 class="auth-card__title" data-pf="login_card_title">{{ $l['card_title'] }}</h2>
                <p class="auth-card__subtitle" data-pf="login_card_subtitle">{{ $l['card_subtitle'] }}</p>

                {{-- Akun yang pernah dipakai di PERANGKAT INI. Hanya alamat email
                     yang disimpan (localStorage) — tidak pernah kata sandi. Diisi
                     oleh auth.js; tersembunyi selama kosong. --}}
                <div class="auth-accounts" id="auth-accounts" hidden>
                    <div class="auth-accounts__label">
                        <span>Masuk sebagai</span>
                        <button type="button" class="auth-accounts__clear" data-accounts-clear>Hapus semua</button>
                    </div>
                    <ul class="auth-accounts__list" data-accounts-list></ul>
                </div>

                {{-- Galat dari pengiriman tanpa JavaScript tampil di sini; alur
                     fetch memakai kotak yang sama. --}}
                <div class="auth-alert" id="auth-alert" role="alert" @if (! $galatPertama) hidden @endif>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
                    <span data-alert-text>{{ $galatPertama }}</span>
                </div>

                <form method="POST" action="{{ route('admin.login.post') }}" id="auth-signin-form" novalidate
                      data-redirect="{{ route('admin.dashboard') }}">
                    @csrf

                    <div class="auth-field">
                        <label class="auth-label" for="auth-identifier">Email</label>
                        <div class="auth-input-wrap">
                            <input type="email" name="email" id="auth-identifier" class="auth-input"
                                   value="{{ old('email') }}" placeholder="nama@contoh.com"
                                   autocomplete="username" autocapitalize="none" spellcheck="false" required
                                   @if ($errors->has('email')) aria-invalid="true" @endif>
                        </div>
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="auth-password">Kata sandi</label>
                        <div class="auth-input-wrap">
                            <input type="password" name="password" id="auth-password"
                                   class="auth-input auth-input--password" placeholder="••••••••"
                                   autocomplete="current-password" required>
                            <button type="button" class="auth-toggle" data-auth-toggle-password="auth-password"
                                    aria-label="Tampilkan kata sandi" aria-pressed="false">
                                <svg data-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg data-eye-off viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none"><path d="M3 3l18 18M10.6 10.6a3 3 0 0 0 4.2 4.2M9.9 5.1A10 10 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 4M6.1 6.1A17 17 0 0 0 2 12s3.5 7 10 7a9.7 9.7 0 0 0 5.9-2"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="auth-submit" data-auth-submit data-label-idle="{{ $l['button'] }}" data-label-busy="{{ $l['button_busy'] }}">
                        <span class="auth-submit__spinner" aria-hidden="true"></span>
                        <span data-label data-pf="login_button">{{ $l['button'] }}</span>
                    </button>
                </form>

                <p class="auth-foot"><a href="{{ route('home') }}" data-pf="login_foot">{{ $l['foot'] }}</a></p>
            </div>
        </main>
    </div>

    {{-- Overlay proses masuk --}}
    {{-- Langkah pertama tampil saat overlay muncul; sisanya diputar auth.js. --}}
    <div id="auth-progress" role="status" aria-live="polite" aria-hidden="true" data-steps='@json(array_slice($l['progress'], 1))'>
        <div class="auth-progress__ring" aria-hidden="true"></div>
        <p class="auth-progress__step" data-progress-step data-pf="login_progress_1">{{ $l['progress'][0] ?? 'Memverifikasi kredensial' }}</p>
        <div class="auth-progress__dots" data-progress-dots aria-hidden="true"><i></i><i></i><i></i></div>
    </div>
</body>
</html>
