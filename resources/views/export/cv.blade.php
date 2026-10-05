@php
    use Illuminate\Support\Str;
    $bersih = fn (?string $u) => $u ? (string) Str::of($u)->after('://')->replace('www.', '')->rtrim('/') : null;
    $github = $socials->first(fn ($s) => $s->platform === 'github');
    $pribadi = collect([
        'Nama lengkap' => $identity->full_name,
        'Domisili' => $identity->location,
        'Email' => $identity->contact_email,
        'Telepon / WA' => $identity->wa ? '+' . $identity->wa : null,
        'LinkedIn' => $bersih($identity->contact_linkedin),
        'GitHub' => $bersih($github?->url),
        'Situs' => $bersih(url('/')),
        'Bahasa' => $identity->languages,
    ])->filter();
    $paragraf = Str::of((string) $identity->summary)->explode("\n")->map(fn ($p) => trim($p))->filter();
    $publikasi = $pendidikan->filter(fn ($x) => $x->link_url);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Curriculum Vitae · {{ $identity->full_name }}</title>
@include('export.partials.riwayat-dasar')
<style>
    @page { size: A4; margin: 18mm 18mm 16mm; }
    .judul-dok { font-size: 8pt; font-weight: 700; letter-spacing: .3em; text-transform: uppercase; color: var(--redup); }
    .kepala { display: grid; grid-template-columns: 1fr auto; gap: 18pt; align-items: start; margin-top: 6pt; padding-bottom: 14pt; border-bottom: 2px solid var(--tinta); }
    .kepala h1 { font-size: 26pt; font-weight: 800; letter-spacing: -.02em; line-height: 1.08; }
    .kepala__peran { margin-top: 4pt; font-size: 11.5pt; font-weight: 600; color: var(--aksen); }
    .kepala__foto { width: 78pt; height: 96pt; border-radius: 6pt; object-fit: cover; }
    .data { display: grid; grid-template-columns: 78pt 1fr 78pt 1fr; gap: 4pt 10pt; font-size: 9pt; }
    .data dt { color: var(--redup); }
    .data dd { color: var(--tinta); font-weight: 600; word-break: break-word; }
    .profil p { margin-bottom: 5pt; text-align: justify; }
    .butir__durasi { color: var(--redup); font-weight: 400; }
    .keahlian { display: grid; grid-template-columns: 70pt 1fr; gap: 5pt 12pt; }
    .keahlian dt { font-weight: 700; color: var(--tinta); }
    table { width: 100%; border-collapse: collapse; font-size: 8.8pt; }
    th { text-align: left; font-size: 7.6pt; letter-spacing: .1em; text-transform: uppercase; color: var(--redup); font-weight: 700; padding: 0 6pt 5pt 0; border-bottom: 1px solid var(--garis); }
    td { padding: 6pt 6pt 6pt 0; border-bottom: 1px solid var(--garis); vertical-align: top; }
    tr { break-inside: avoid; }
    td.no { color: var(--redup); width: 18pt; font-variant-numeric: tabular-nums; }
    td b { color: var(--tinta); display: block; }
    td .kecil { display: block; margin-top: 2pt; }
    .publikasi { padding: 8pt 10pt; border-left: 2px solid var(--aksen); background: var(--aksen-muda); break-inside: avoid; }
    .publikasi b { color: var(--tinta); }
    .tanda { margin-top: 22pt; font-size: 8.4pt; color: var(--redup); break-inside: avoid; }
</style>
</head>
<body>
    <p class="judul-dok">Curriculum Vitae</p>
    <header class="kepala">
        <div>
            <h1>{{ $identity->full_name }}</h1>
            <p class="kepala__peran">{{ $identity->headline ?: $identity->topbar_role_text }}</p>
        </div>
        @if ($foto)<img class="kepala__foto" src="{{ $foto }}" alt="">@endif
    </header>

    <section class="bagian">
        <h2>Data Pribadi</h2>
        <dl class="data">
            @foreach ($pribadi as $k => $v)<dt>{{ $k }}</dt><dd>{{ $v }}</dd>@endforeach
        </dl>
    </section>

    @if ($paragraf->isNotEmpty())
    <section class="bagian profil">
        <h2>Profil</h2>
        @foreach ($paragraf as $p)<p>{{ $p }}</p>@endforeach
    </section>
    @endif

    @if ($kerja->isNotEmpty())
    <section class="bagian">
        <h2>Riwayat Pekerjaan</h2>
        @foreach ($kerja as $x)
            <div class="butir">
                <div class="butir__atas">
                    <span class="butir__judul">{{ $x->title }}</span>
                    <span class="butir__waktu">{{ $x->periode }} <span class="butir__durasi">({{ $x->durasi }})</span></span>
                </div>
                <div class="butir__org">{{ $x->organization }}@if ($x->employment_type) <span>· {{ $x->employment_type }}</span>@endif @if ($x->location)<span>· {{ $x->location }}</span>@endif</div>
                @if ($x->poin)<ul>@foreach ($x->poin as $p)<li>{{ $p }}</li>@endforeach</ul>@endif
            </div>
        @endforeach
    </section>
    @endif

    @if ($pendidikan->isNotEmpty())
    <section class="bagian">
        <h2>Pendidikan</h2>
        @foreach ($pendidikan as $x)
            <div class="butir">
                <div class="butir__atas">
                    <span class="butir__judul">{{ $x->title }}</span>
                    <span class="butir__waktu">{{ $x->periode }}</span>
                </div>
                <div class="butir__org">{{ $x->organization }}@if ($x->location) <span>· {{ $x->location }}</span>@endif</div>
                @if ($x->grade)<p style="margin-top:2pt;">IPK <b style="color:var(--tinta)">{{ $x->grade }}</b></p>@endif
                @if ($x->poin)<ul>@foreach ($x->poin as $p)<li>{{ $p }}</li>@endforeach</ul>@endif
            </div>
        @endforeach
    </section>
    @endif

    @if ($publikasi->isNotEmpty())
    <section class="bagian">
        <h2>Publikasi</h2>
        @foreach ($publikasi as $x)
            <p class="publikasi"><b>{{ Str::of($x->link_label ?: 'Publikasi')->after('Jurnal: ') }}</b><br><span class="kecil">{{ $x->link_url }}</span></p>
        @endforeach
    </section>
    @endif

    @if ($organisasi->isNotEmpty())
    <section class="bagian">
        <h2>Organisasi</h2>
        @foreach ($organisasi as $x)
            <div class="butir">
                <div class="butir__atas"><span class="butir__judul">{{ $x->title }}</span><span class="butir__waktu">{{ $x->periode }}</span></div>
                <div class="butir__org">{{ $x->organization }}</div>
                @if ($x->poin)<ul>@foreach ($x->poin as $p)<li>{{ $p }}</li>@endforeach</ul>@endif
            </div>
        @endforeach
    </section>
    @endif

    @if ($teknologi->isNotEmpty())
    <section class="bagian">
        <h2>Keahlian Teknis</h2>
        <dl class="keahlian">
            @foreach ($teknologi as $kelompok => $isi)<dt>{{ $kelompok }}</dt><dd>{{ $isi->pluck('name')->implode(', ') }}</dd>@endforeach
        </dl>
    </section>
    @endif

    @if ($proyek->isNotEmpty())
    <section class="bagian">
        <h2>Daftar Proyek</h2>
        <table>
            <thead><tr><th></th><th>Proyek</th><th style="width:62pt">Jenis</th><th style="width:34pt">Tahun</th><th style="width:130pt">Teknologi</th></tr></thead>
            <tbody>
                @foreach ($proyek as $i => $p)
                    <tr>
                        <td class="no">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</td>
                        <td><b>{{ $p->title }}</b><span class="kecil">{{ $p->short_description }}</span></td>
                        <td>{{ $p->category }}</td>
                        <td>{{ $p->year }}</td>
                        <td>{{ implode(', ', $p->tech_stack ?? []) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
    @endif

    <p class="tanda">Dokumen ini dibuat dari {{ $bersih(url('/')) }} pada {{ now()->locale('id')->translatedFormat('j F Y') }}. Rincian dan gambar tiap proyek tersedia di situs tersebut.<br>Keaslian dokumen dapat diperiksa di {{ $bersih(url('/')) }}/verifikasi/{{ \App\Services\PortfolioPdfService::PENANDA_KODE }}</p>
</body>
</html>
