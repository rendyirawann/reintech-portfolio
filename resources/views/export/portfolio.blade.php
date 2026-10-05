@php
    use App\Support\Platform;
    use Illuminate\Support\Str;

    $nama = $identity->full_name ?: ($identity->logo_text ?: 'Portofolio');
    $headline = $identity->headline ?: ($bagian['hero']->subtitle ?? $identity->topbar_role_text);

    // Profil: yang paling mungkin ditulis untuk dibaca orang lebih dulu.
    $profil = $identity->meta_description
        ?: ($bagian['about']->description ?? null)
        ?: ($bagian['hero']->description ?? '');

    // Teknologi dirangkum dari proyek yang benar-benar dikerjakan, bukan
    // daftar terpisah — dokumen ini tidak pernah mengklaim sesuatu yang tidak
    // ada buktinya di halaman proyek.
    // Daftar tech stack yang dikelola di admin menang; cadangannya rangkuman proyek.
    $teknologi = ($teknologiKelola ?? collect())->isNotEmpty() ? $teknologiKelola->take(18) : $projects->pluck('tech_stack')->flatten()->filter()
        ->map(fn ($t) => trim((string) $t))->filter()
        ->countBy()->sortDesc()->keys()->take(14);

    $wa = $identity->wa;
    $situs = Str::of(url('/'))->after('://')->rtrim('/');

    $kontak = collect([
        $identity->contact_email ? ['Email', $identity->contact_email] : null,
        $wa ? ['WhatsApp', '+' . $wa] : null,
        $identity->location ? ['Lokasi', $identity->location] : null,
        ['Situs', (string) $situs],
        $identity->contact_linkedin ? ['LinkedIn', (string) Str::of($identity->contact_linkedin)->after('://')->replaceFirst('www.', '')->rtrim('/')] : null,
    ])->filter();

    // Dua versi, satu templat: halaman profil identik; bedanya hanya halaman
    // proyek. Bergambar = satu proyek per lembar. Daftar = PER_LEMBAR proyek
    // per lembar, tanpa foto.
    $modeDaftar = $modeDaftar ?? false;
    $perLembar = $modeDaftar ? 6 : 1;   // daftar: 2 kolom × 3 baris
    // Indeks di halaman 1 harus muat satu halaman: dua kolom bila proyeknya banyak.
    $indeksRapat = $projects->count() > 8;
    $lembarProyek = $projects->chunk($perLembar)->values();
    $jumlahHalaman = 1 + $lembarProyek->count();
    $nomor = fn (int $n) => str_pad((string) $n, 2, '0', STR_PAD_LEFT);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Portofolio · {{ $nama }}</title>
<style>
    @if ($font)
    @font-face {
        font-family: 'Plus Jakarta Sans';
        src: url('{{ $font }}') format('woff2');
        font-weight: 200 800;
    }
    @endif

    /*
     * Dokumen portofolio A4.
     *
     * Kualitas strukturnya mengikuti ekspor portfolio-pm — halaman A4 tetap,
     * satu halaman per proyek, nomor halaman di kaki — tetapi tata letaknya
     * sengaja dibuat berbeda supaya kedua portofolio tidak tertukar:
     *
     *   portfolio-pm   pita gelap mendatar di atas, foto di kiri, aksen merah,
     *                  sampul proyek selebar halaman lalu teks di bawahnya
     *   REINTECH       lajur gelap TEGAK di kiri (foto, kontak, teknologi),
     *                  aksen biru tua, sampul proyek di kiri berdampingan
     *                  dengan panel data di kanan
     *
     * Tanpa ungu, tanpa gradasi berlebihan, Plus Jakarta Sans di seluruhnya.
     */
    @page { size: A4; margin: 0; }

    :root {
        --tinta: #0b1220;
        --teks: #1e293b;
        --redup: #64748b;
        --garis: #e2e8f0;
        --lembut: #f4f7fb;
        --aksen: #1e40af;
        --aksen-2: #0891b2;
        --lajur: #0b1220;
    }

    * { box-sizing: border-box; }
    html, body { margin: 0; }
    body {
        font: 400 9.6pt/1.55 'Plus Jakarta Sans', 'Helvetica Neue', Arial, sans-serif;
        color: var(--teks);
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    h1, h2, h3, h4, p { margin: 0; }
    ul { margin: 0; padding: 0; list-style: none; }
    a { color: inherit; text-decoration: none; }

    .lembar {
        position: relative;
        width: 210mm;
        /* Tinggi minimum, bukan tetap: isi yang lebih panjang berlanjut ke
           halaman berikutnya alih-alih terpotong tak terlihat. */
        min-height: 297mm;
        display: flex;
        flex-direction: column;
        background: #fff;
        break-after: page;
        page-break-after: always;
    }
    .lembar:last-child { break-after: auto; page-break-after: auto; }

    .label {
        font-size: 7pt;
        font-weight: 700;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: var(--aksen);
    }

    .judul-bagian {
        display: flex;
        align-items: center;
        gap: 3mm;
        margin: 0 0 3.5mm;
        font-size: 7.6pt;
        font-weight: 700;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: var(--tinta);
    }
    .judul-bagian::before {
        content: '';
        width: 5mm;
        height: 1.4pt;
        background: var(--aksen);
    }

    /* ═════════════ HALAMAN 1 — PROFIL ═════════════ */

    .profil {
        display: grid;
        grid-template-columns: 64mm 1fr;
        height: 100%;
    }

    /* Lajur gelap tegak */
    .lajur {
        background: var(--lajur);
        color: #cbd5e1;
        padding: 14mm 8mm 12mm;
        display: flex;
        flex-direction: column;
        gap: 8mm;
    }
    .lajur__merek {
        display: flex;
        align-items: center;
        gap: 2.2mm;
        font-size: 7pt;
        font-weight: 700;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: #94a3b8;
    }
    .lajur__merek img { width: 6mm; height: 6mm; }
    .foto {
        width: 100%;
        aspect-ratio: 4 / 5;
        border-radius: 3mm;
        object-fit: cover;
        object-position: center 20%;
        background: #1e293b;
        /* Garis cyan tipis di bawah foto: satu-satunya aksen terang di lajur. */
        border-bottom: 1.2mm solid var(--aksen-2);
    }
    .foto--kosong {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30pt;
        font-weight: 800;
        color: #334155;
    }
    .lajur h4 {
        margin-bottom: 2.5mm;
        font-size: 6.8pt;
        font-weight: 700;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: #7dd3fc;
    }
    .lajur dl { margin: 0; display: grid; gap: 2.4mm; }
    .lajur dt { font-size: 6.6pt; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: #64748b; }
    .lajur dd { margin: .4mm 0 0; font-size: 8.3pt; color: #e2e8f0; word-break: break-word; }
    .lajur .cip { display: flex; flex-wrap: wrap; gap: 1.4mm; }
    .lajur .cip span {
        padding: .8mm 2.2mm;
        border: .5pt solid #1f2c45;
        border-radius: 1mm;
        font-size: 7.4pt;
        color: #cbd5e1;
    }

    /* Kolom utama */
    .utama {
        padding: 16mm 14mm 0 12mm;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    .nama {
        margin-top: 2.5mm;
        font-size: 27pt;
        font-weight: 800;
        line-height: 1.04;
        letter-spacing: -.025em;
        color: var(--tinta);
    }
    .headline {
        margin-top: 2.5mm;
        font-size: 11pt;
        font-weight: 600;
        color: var(--aksen);
    }
    .ringkasan {
        margin-top: 7mm;
        font-size: 10pt;
        line-height: 1.72;
        color: var(--teks);
    }

    .angka {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
        margin-top: 7mm;
        border-top: .5pt solid var(--garis);
        border-bottom: .5pt solid var(--garis);
    }
    .angka li { padding: 4mm 3mm; border-left: .5pt solid var(--garis); }
    .angka li:first-child { border-left: 0; padding-left: 0; }
    .angka b { display: block; font-size: 17pt; font-weight: 800; color: var(--tinta); line-height: 1.05; }
    .angka span { font-size: 7.4pt; color: var(--redup); }

    .blok { margin-top: 8mm; }

    .layanan { display: grid; grid-template-columns: 1fr 1fr; gap: 3mm 6mm; }
    .layanan li { padding-left: 3mm; border-left: 1.2pt solid var(--garis); }
    .layanan b { display: block; font-size: 9pt; font-weight: 700; color: var(--tinta); }
    .layanan--judul { gap: 1.8mm 6mm; }
    .layanan--judul li { padding: .6mm 0 .6mm 3mm; }
    .layanan span { font-size: 8.2pt; color: var(--redup); }

    /* Daftar isi proyek — menunjuk halaman tempat proyeknya diuraikan. */
    .indeks li {
        display: grid;
        grid-template-columns: 9mm 1fr auto;
        align-items: baseline;
        gap: 3mm;
        padding: 2.6mm 0;
        border-bottom: .5pt dashed var(--garis);
    }
    .indeks li:last-child { border-bottom: 0; }
    .indeks--rapat { display: grid; grid-template-columns: 1fr 1fr; column-gap: 7mm; }
    .indeks--rapat li { padding-top: 1.5mm; padding-bottom: 1.5mm; align-items: baseline; grid-template-columns: 7mm 1fr auto; }
    .indeks--rapat li:nth-last-child(2):nth-child(odd) { border-bottom: 0; }
    .indeks--rapat .t { font-size: 8.8pt; }
    .indeks--rapat .t small { display: none; }
    .indeks--rapat .hal { font-size: 6.8pt; }
    .indeks .no { font-size: 8pt; font-weight: 800; color: var(--aksen); }
    .indeks .t { font-size: 10pt; font-weight: 700; color: var(--tinta); }
    .indeks .t small { display: block; font-size: 7.8pt; font-weight: 500; color: var(--redup); }
    .indeks .hal { font-size: 7.6pt; font-weight: 600; color: var(--redup); letter-spacing: .06em; }

    .kaki-profil {
        margin-top: auto;
        padding: 6mm 0 9mm;
        display: flex;
        justify-content: space-between;
        font-size: 7.4pt;
        color: var(--redup);
        border-top: .5pt solid var(--garis);
    }
    .kaki-profil b { color: var(--tinta); font-weight: 600; }

    /* ═════════════ HALAMAN PROYEK ═════════════ */

    .kepala-proyek {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9mm 14mm 5mm;
        border-bottom: .5pt solid var(--garis);
    }
    .kepala-proyek .merek {
        display: flex;
        align-items: center;
        gap: 2.2mm;
        font-size: 7pt;
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: var(--redup);
    }
    .kepala-proyek .merek img { width: 5.4mm; height: 5.4mm; }
    .kepala-proyek .urut {
        font-size: 7.4pt;
        font-weight: 700;
        letter-spacing: .14em;
        color: var(--aksen);
    }

    .isi-proyek {
        flex: 1;
        min-height: 0;
        padding: 9mm 14mm 0;
        display: flex;
        flex-direction: column;
    }
    .judul-proyek {
        margin-top: 2mm;
        font-size: 21pt;
        font-weight: 800;
        line-height: 1.12;
        letter-spacing: -.02em;
        color: var(--tinta);
        max-width: 150mm;
    }
    .ringkas-proyek {
        margin-top: 3mm;
        font-size: 10.6pt;
        font-weight: 500;
        line-height: 1.55;
        color: var(--teks);
        max-width: 160mm;
    }

    /* Sampul di kiri, panel data di kanan — kebalikan dari portfolio-pm yang
       memasang sampul selebar halaman. */
    .dua {
        display: grid;
        grid-template-columns: 1fr 54mm;
        gap: 7mm;
        margin-top: 7mm;
    }
    .sampul {
        width: 100%;
        height: 86mm;
        border-radius: 2.5mm;
        object-fit: cover;
        background: var(--lembut);
        border: .5pt solid var(--garis);
    }
    .sampul--kosong {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30pt;
        font-weight: 800;
        color: #cbd5e1;
    }
    .data {
        padding: 5mm;
        border-radius: 2.5mm;
        background: var(--lembut);
        border-top: 1.2mm solid var(--aksen);
        display: grid;
        gap: 4mm;
        align-content: start;
    }
    .data dt { font-size: 6.8pt; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--redup); }
    .data dd { margin: .8mm 0 0; font-size: 9pt; font-weight: 600; color: var(--tinta); word-break: break-word; }
    .data .cip { display: flex; flex-wrap: wrap; gap: 1.3mm; margin-top: 1.2mm; }
    .data .cip span {
        padding: .8mm 2.1mm;
        border-radius: 1mm;
        background: #fff;
        border: .5pt solid var(--garis);
        font-size: 7.4pt;
        font-weight: 600;
        color: var(--aksen);
    }
    .data .tautan { display: grid; gap: 1.6mm; margin-top: 1.2mm; }
    .data .tautan a { display: flex; align-items: center; gap: 1.6mm; font-size: 8pt; font-weight: 600; color: var(--teks); }
    .data .tautan svg { width: 3.4mm; height: 3.4mm; flex: none; color: var(--aksen); }

    .uraian {
        margin-top: 7mm;
        font-size: 9.2pt;
        line-height: 1.72;
        color: var(--teks);
        white-space: pre-line;
    }

    .galeri {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 3mm;
        margin-top: 6mm;
    }
    .galeri img {
        width: 100%;
        height: 30mm;
        border-radius: 1.8mm;
        object-fit: cover;
        background: var(--lembut);
        border: .5pt solid var(--garis);
    }

    .kaki-proyek {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        padding: 5mm 14mm 8mm;
        font-size: 7.4pt;
        color: var(--redup);
    }
    .segel { font-size: 6.4pt; letter-spacing: .02em; color: var(--redup); font-variant-numeric: tabular-nums; }
    .kaki-proyek b { color: var(--tinta); font-weight: 600; }

    /* ── versi daftar ── */
    /* Dua kolom × tiga baris per lembar; tiap kartu sama tinggi dalam barisnya. */
    .daftar-grid { display: grid; grid-template-columns: 1fr 1fr; grid-auto-rows: auto; gap: 0 7mm; align-content: start; }
    .daftar-item { display: grid; grid-template-columns: 7mm 1fr; gap: 0 2mm; padding: 4.5mm 0; border-bottom: .5pt solid var(--garis); break-inside: avoid; page-break-inside: avoid; }
    .daftar-grid .daftar-item:nth-last-child(-n+2):nth-child(odd), .daftar-grid .daftar-item:last-child { border-bottom: 0; }
    .daftar-no { font-size: 9.5pt; font-weight: 800; color: var(--aksen); font-variant-numeric: tabular-nums; padding-top: 3.8mm; }
    .daftar-judul { margin-top: 1mm; font-size: 11.5pt; font-weight: 800; line-height: 1.2; letter-spacing: -.01em; color: var(--tinta); }
    .daftar-item .ringkas-proyek { margin-top: 1.2mm; font-size: 8.6pt; line-height: 1.45; max-width: none; }
    .daftar-uraian { margin-top: 1.6mm; font-size: 7.8pt; line-height: 1.55; color: var(--teks); white-space: pre-line; }
    .daftar-bawah { display: flex; flex-wrap: wrap; align-items: center; gap: 2mm 4mm; margin-top: 2.5mm; }
    .daftar-bawah .cip { display: flex; flex-wrap: wrap; gap: 1.3mm; }
    .daftar-bawah .cip span { padding: .6mm 1.8mm; border-radius: 1mm; background: var(--lembut); border: .5pt solid var(--garis); font-size: 6.9pt; font-weight: 600; color: var(--aksen); }
    .daftar-bawah .tautan { display: flex; flex-wrap: wrap; gap: 1.5mm 4mm; }
    .daftar-bawah .tautan a { display: inline-flex; align-items: center; gap: 1.2mm; font-size: 7.4pt; font-weight: 600; color: var(--tinta); }
    .daftar-bawah .tautan svg { width: 3.2mm; height: 3.2mm; color: var(--aksen); }

    .kosong {
        margin-top: 8mm;
        padding: 8mm;
        border: .5pt dashed var(--garis);
        border-radius: 2mm;
        text-align: center;
        color: var(--redup);
    }
</style>
</head>
<body>

{{-- ════════ HALAMAN 1 — PROFIL ════════ --}}
<section class="lembar">
    <div class="profil">
        <aside class="lajur">
            <div class="lajur__merek">
                @if ($logo)<img src="{{ $logo }}" alt="">@endif
                <span>{{ $identity->logo_subtext ?: 'Portofolio' }}</span>
            </div>

            @if ($foto)
                <img class="foto" src="{{ $foto }}" alt="Foto {{ $nama }}">
            @else
                <div class="foto foto--kosong">{{ mb_strtoupper(mb_substr($nama, 0, 1)) }}</div>
            @endif

            @if ($kontak->isNotEmpty())
                <div>
                    <h4>Kontak</h4>
                    <dl>
                        @foreach ($kontak as [$label, $nilai])
                            <div><dt>{{ $label }}</dt><dd>{{ $nilai }}</dd></div>
                        @endforeach
                    </dl>
                </div>
            @endif

            @if ($teknologi->isNotEmpty())
                <div>
                    <h4>Teknologi</h4>
                    <div class="cip">
                        @foreach ($teknologi as $t)<span>{{ $t }}</span>@endforeach
                    </div>
                </div>
            @endif
        </aside>

        <div class="utama">
            <p class="label">Portofolio {{ now()->year }}</p>
            <h1 class="nama">{{ $nama }}</h1>
            @if ($headline)<p class="headline">{{ $headline }}</p>@endif

            @if (filled($profil))
                <p class="ringkasan">{{ strip_tags($profil) }}</p>
            @endif

            @if ($stats->isNotEmpty())
                <ul class="angka">
                    @foreach ($stats->take(4) as $s)
                        <li><b>{{ $s->nilai() }}{{ $s->suffix }}</b><span>{{ $s->label }}</span></li>
                    @endforeach
                </ul>
            @endif

            @if ($layanan->isNotEmpty())
                <div class="blok">
                    <h2 class="judul-bagian">Layanan</h2>
                    {{-- Hanya nama bidang: halaman profil juga memuat indeks semua proyek.
                         Uraian tiap layanan ada di situs. --}}
                    <ul class="layanan layanan--judul">
                        @foreach ($layanan as $l)
                            <li><b>{{ $l->title }}</b></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="blok">
                <h2 class="judul-bagian">Proyek Pilihan</h2>
                @if ($projects->isNotEmpty())
                    <ul class="indeks @if ($indeksRapat) indeks--rapat @endif">
                        @foreach ($projects as $p)
                            <li>
                                <span class="no">{{ $nomor($loop->iteration) }}</span>
                                <span class="t">
                                    {{ $p->title }}
                                    @php $meta = collect([$p->category, $p->year])->filter()->implode(' · '); @endphp
                                    @if ($meta)<small>{{ $meta }}</small>@endif
                                </span>
                                <span class="hal">HAL. {{ $nomor(intdiv($loop->index, $perLembar) + 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="kosong">Belum ada proyek yang ditampilkan di situs.</div>
                @endif
            </div>

            <footer class="kaki-profil">
                <span><b>{{ $nama }}</b> · {{ $situs }}</span>
                <span class="segel">Verifikasi keaslian: {{ $situs }}/verifikasi/{{ \App\Services\PortfolioPdfService::PENANDA_KODE }}</span>
                <span>{{ $nomor(1) }} / {{ $nomor($jumlahHalaman) }}</span>
            </footer>
        </div>
    </div>
</section>

{{-- ════════ HALAMAN PROYEK — VERSI DAFTAR ════════ --}}
@if ($modeDaftar)
@foreach ($lembarProyek as $h => $kelompok)
    <section class="lembar">
        <header class="kepala-proyek">
            <span class="merek">@if ($logo)<img src="{{ $logo }}" alt="">@endif {{ $nama }}</span>
            <span class="urut">DAFTAR PROYEK {{ $nomor($h + 1) }} / {{ $nomor($lembarProyek->count()) }}</span>
        </header>

        <div class="isi-proyek daftar-grid">
            @foreach ($kelompok as $i => $p)
                @php
                    $meta = collect([$p->category, $p->year])->filter()->implode(' · ');
                    $uraian = trim(strip_tags((string) $p->long_description));
                    $tautan = collect([
                        $p->live_url ? ['Situs', $p->live_url] : null,
                        $p->repo_url ? ['Repositori', $p->repo_url] : null,
                    ])->filter()->merge($p->links->map(fn ($l) => [$l->label, $l->url]))
                      ->unique(fn ($t) => rtrim($t[1], '/'));
                @endphp
                <article class="daftar-item">
                    <span class="daftar-no">{{ $nomor($i + 1) }}</span>
                    <div>
                        <p class="label">{{ $meta ?: 'Proyek' }}</p>
                        <h2 class="daftar-judul">{{ $p->title }}</h2>
                        @if ($p->short_description)<p class="ringkas-proyek">{{ $p->short_description }}</p>@endif
                        @if ($uraian && $uraian !== trim((string) $p->short_description))<p class="daftar-uraian">{{ $uraian }}</p>@endif
                        <div class="daftar-bawah">
                            @if (! empty($p->tech_stack))
                                <span class="cip">@foreach ($p->tech_stack as $t)<span>{{ $t }}</span>@endforeach</span>
                            @endif
                            @if ($tautan->isNotEmpty())
                                <span class="tautan">
                                    @foreach ($tautan as [$label, $url])
                                        <a href="{{ $url }}">{!! Platform::ikonDariUrl($url) !!}{{ $label }}</a>
                                    @endforeach
                                </span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <footer class="kaki-proyek">
            <span><b>{{ $nama }}</b> · Daftar Proyek</span>
                <span class="segel">Verifikasi keaslian: {{ $situs }}/verifikasi/{{ \App\Services\PortfolioPdfService::PENANDA_KODE }}</span>
            <span>{{ $nomor($h + 2) }} / {{ $nomor($jumlahHalaman) }}</span>
        </footer>
    </section>
@endforeach
@else
{{-- ════════ HALAMAN PROYEK — VERSI BERGAMBAR ════════ --}}
@foreach ($projects as $p)
    @php
        $sampul = $gambarProyek[$p->id] ?? null;
        $fotoGaleri = ($galeri[$p->id] ?? collect())->reject(fn ($u) => $u === $sampul)->take(3);
        $uraian = $p->long_description ?: $p->short_description;
        $tautan = $p->links->map(fn ($l) => [$l->label, $l->url])
            ->when($p->links->isEmpty(), fn ($c) => $c
                ->push($p->live_url ? ['Demo', $p->live_url] : null)
                ->push($p->repo_url ? ['Repositori', $p->repo_url] : null)
                ->filter());
    @endphp
    <section class="lembar">
        <header class="kepala-proyek">
            <span class="merek">@if ($logo)<img src="{{ $logo }}" alt="">@endif {{ $nama }}</span>
            <span class="urut">PROYEK {{ $nomor($loop->iteration) }} / {{ $nomor($projects->count()) }}</span>
        </header>

        <div class="isi-proyek">
            @php $meta = collect([$p->category, $p->year])->filter()->implode(' · '); @endphp
            <p class="label">{{ $meta ?: 'Proyek Pilihan' }}</p>
            <h2 class="judul-proyek">{{ $p->title }}</h2>
            @if ($p->short_description && $p->long_description)
                <p class="ringkas-proyek">{{ $p->short_description }}</p>
            @endif

            <div class="dua">
                @if ($sampul)
                    <img class="sampul" src="{{ $sampul }}" alt="{{ $p->title }}">
                @else
                    <div class="sampul sampul--kosong">{{ mb_strtoupper(mb_substr($p->title, 0, 2)) }}</div>
                @endif

                <dl class="data">
                    @if ($p->year)<div><dt>Tahun</dt><dd>{{ $p->year }}</dd></div>@endif
                    @if ($p->category)<div><dt>Kategori</dt><dd>{{ $p->category }}</dd></div>@endif
                    @if (! empty($p->tech_stack))
                        <div>
                            <dt>Teknologi</dt>
                            <dd class="cip">@foreach ($p->tech_stack as $t)<span>{{ $t }}</span>@endforeach</dd>
                        </div>
                    @endif
                    @if ($tautan->isNotEmpty())
                        <div>
                            <dt>Tautan</dt>
                            <dd class="tautan">
                                @foreach ($tautan->take(4) as [$label, $url])
                                    <a href="{{ $url }}">{!! Platform::ikonDariUrl($url) !!}{{ $label }}</a>
                                @endforeach
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            @if ($uraian)
                <p class="uraian">{{ strip_tags((string) $uraian) }}</p>
            @endif

            @if ($fotoGaleri->isNotEmpty())
                <div class="galeri">
                    @foreach ($fotoGaleri as $src)<img src="{{ $src }}" alt="">@endforeach
                </div>
            @endif
        </div>

        <footer class="kaki-proyek">
            <span><b>{{ $nama }}</b> · Proyek Pilihan</span>
                <span class="segel">Verifikasi keaslian: {{ $situs }}/verifikasi/{{ \App\Services\PortfolioPdfService::PENANDA_KODE }}</span>
            <span>{{ $nomor($loop->iteration + 1) }} / {{ $nomor($jumlahHalaman) }}</span>
        </footer>
    </section>
@endforeach
@endif

<script>
    // Setiap lembar wajib pas satu halaman A4. Bila isinya melebihi, kolom
    // isi (bukan seluruh lembar) diperkecil secukupnya — paling kecil 80%.
    (function () {
        var A4 = 297 * 96 / 25.4;
        document.querySelectorAll('.lembar').forEach(function (lembar) {
            var lebih = lembar.getBoundingClientRect().height - A4;
            var isi = lembar.querySelector('.utama, .isi-proyek');
            if (lebih <= 0 || !isi) return;
            var h = isi.getBoundingClientRect().height;
            isi.style.zoom = String(Math.max(0.8, (h - lebih - 4) / h));
        });
    })();
</script>
</body>
</html>
