{{--
    Kepala SEO halaman depan.

    Seluruh isinya berasal dari `portfolio_identity`, jadi bisa disunting dari
    /admin/identity tanpa menyentuh kode. Yang tidak diisi mundur ke nilai yang
    masuk akal, bukan ke tag kosong — tag kosong lebih buruk daripada tidak ada
    tag sama sekali, karena mesin pencari tetap memakainya.

    Canonical memakai url()->current() dan BUKAN request()->fullUrl(): dengan
    fullUrl, setiap tautan kampanye (?utm_source=...) melahirkan alamat kanonis
    yang berbeda, dan mesin pencari memperlakukannya sebagai halaman terpisah
    yang saling menggandakan isi.
--}}
@php
    $seoJudul = $identity->meta_title
        ?: trim(($identity->logo_text ?? 'REINTECH') . ' | ' . ($identity->logo_subtext ?? 'Digital Universe Creator'));

    $seoDeskripsi = $identity->meta_description
        ?: ($sections['hero']->subtitle ?? 'Full Stack Developer / Senior Programmer.');

    $seoGambar = $identity->og_image
        ? asset('storage/' . $identity->og_image)
        : asset('build/assets/og-default.png');

    $kanonis = url()->current();
@endphp

<title>{{ $seoJudul }}</title>
<meta name="description" content="{{ Str::limit(strip_tags($seoDeskripsi), 300, '') }}">
@if ($identity->meta_keywords)
    <meta name="keywords" content="{{ $identity->meta_keywords }}">
@endif
<link rel="canonical" href="{{ $kanonis }}">
<meta name="robots" content="index, follow, max-image-preview:large">
<meta name="theme-color" content="#0B0B10">

{{-- Favicon & ikon aplikasi. SVG didahulukan: satu berkas untuk semua
     kepadatan layar, dan peramban yang belum mendukungnya mundur ke PNG. --}}
<link rel="icon" type="image/svg+xml" href="{{ asset('images/reintech-logo.svg') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
<link rel="manifest" href="{{ route('manifest') }}">

{{-- Open Graph: dipakai WhatsApp, LinkedIn, Facebook, dan Telegram --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $identity->logo_text ?? 'REINTECH' }}">
<meta property="og:title" content="{{ $seoJudul }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($seoDeskripsi), 300, '') }}">
<meta property="og:url" content="{{ $kanonis }}">
<meta property="og:image" content="{{ $seoGambar }}">
<meta property="og:locale" content="id_ID">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoJudul }}">
<meta name="twitter:description" content="{{ Str::limit(strip_tags($seoDeskripsi), 200, '') }}">
<meta name="twitter:image" content="{{ $seoGambar }}">

{{--
    Data terstruktur. Person + WebSite dalam satu @graph, bukan dua blok
    terpisah, supaya keduanya bisa saling menunjuk lewat @id — itu yang
    membuat mesin pencari tahu situs ini milik orang tersebut.

    Dirakit dengan json_encode dari array, bukan ditulis tangan sebagai string:
    tanda kutip pada nama atau deskripsi akan merusak JSON yang ditulis tangan.
--}}
@php
    $tautanSosial = $socials->pluck('url')->filter()->values()->all();

    $grafik = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Person',
                '@id' => url('/') . '#orang',
                'name' => $identity->logo_text ?? 'REINTECH',
                'jobTitle' => $identity->topbar_role_text ?? 'Full Stack Developer / Senior Programmer',
                'description' => Str::limit(strip_tags($seoDeskripsi), 300, ''),
                'url' => url('/'),
                'sameAs' => $tautanSosial,
            ],
            [
                '@type' => 'WebSite',
                '@id' => url('/') . '#situs',
                'name' => $seoJudul,
                'url' => url('/'),
                'inLanguage' => 'id-ID',
                'publisher' => ['@id' => url('/') . '#orang'],
            ],
        ],
    ];

    if ($projects->isNotEmpty()) {
        $grafik['@graph'][] = [
            '@type' => 'ItemList',
            'name' => 'Proyek',
            'itemListElement' => $projects->take(20)->values()->map(fn ($p, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $p->title,
                'description' => Str::limit(strip_tags((string) $p->short_description), 200, ''),
            ])->all(),
        ];
    }
@endphp
<script type="application/ld+json">{!! json_encode($grafik, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
