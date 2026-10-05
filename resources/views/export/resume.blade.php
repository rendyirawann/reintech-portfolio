@php
    use Illuminate\Support\Str;
    // Resume: SATU halaman. Semua batas di bawah ini ada untuk menjaganya.
    $ringkas = Str::of((string) $identity->summary)->explode("\n")->map(fn ($p) => trim($p))->filter()->first();
    $github = $socials->first(fn ($s) => $s->platform === 'github');
    $kontak = collect([
        $identity->location,
        $identity->contact_email,
        $identity->wa ? '+' . $identity->wa : null,
        $identity->contact_linkedin ? Str::of($identity->contact_linkedin)->after('://')->replace('www.', '')->rtrim('/') : null,
        $github ? Str::of($github->url)->after('://')->replace('www.', '')->rtrim('/') : null,
        Str::of(url('/'))->after('://')->rtrim('/'),
    ])->filter();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Resume · {{ $identity->full_name }}</title>
@include('export.partials.riwayat-dasar')
<style>
    @page { size: A4; margin: 12mm 15mm 11mm; }
    body { font-size: 9pt; line-height: 1.42; width: 180mm; }
    .bagian { margin-top: 10pt; }
    .bagian > h2 { margin-bottom: 6pt; }
    .butir { margin-bottom: 6pt; }
    .butir ul { margin-top: 2pt; }
    .kepala { display: flex; justify-content: space-between; align-items: flex-end; gap: 16pt; padding-bottom: 10pt; border-bottom: 2px solid var(--tinta); }
    .kepala h1 { font-size: 23pt; font-weight: 800; letter-spacing: -.02em; line-height: 1.1; }
    .kepala__peran { margin-top: 3pt; font-size: 11pt; font-weight: 600; color: var(--aksen); }
    .kepala img { height: 30pt; }
    .kontak { display: flex; flex-wrap: wrap; gap: 2pt 0; margin-top: 7pt; font-size: 8.6pt; color: var(--isi); }
    .kontak span:not(:last-child)::after { content: "·"; margin: 0 6pt; color: var(--redup); }
    .ringkasan { margin-top: 10pt; font-size: 9.8pt; color: var(--isi); }
    .keahlian { display: grid; grid-template-columns: 62pt 1fr; gap: 3pt 10pt; }
    .keahlian dt { font-weight: 700; color: var(--tinta); }
    .proyek-baris { break-inside: avoid; margin-bottom: 5pt; }
    .proyek-baris b { color: var(--tinta); }
    .proyek-baris .kecil { margin-left: 4pt; }
</style>
</head>
<body>
    <header class="kepala">
        <div>
            <h1>{{ $identity->full_name }}</h1>
            <p class="kepala__peran">{{ $identity->topbar_role_text ?: $identity->headline }}</p>
            <p class="kontak">@foreach ($kontak as $k)<span>{{ $k }}</span>@endforeach</p>
        </div>
    </header>

    @if ($ringkas)<p class="ringkasan">{{ $ringkas }}</p>@endif

    @if ($kerja->isNotEmpty())
    <section class="bagian">
        <h2>Pengalaman</h2>
        @foreach ($kerja->take(4) as $x)
            <div class="butir">
                <div class="butir__atas">
                    <span class="butir__judul">{{ $x->title }}</span>
                    <span class="butir__waktu">{{ $x->periode }}</span>
                </div>
                <div class="butir__org">{{ $x->organization }}@if ($x->location) <span>· {{ $x->location }}</span>@endif</div>
                @if ($x->poin)
                    <ul>@foreach (array_slice($x->poin, 0, 2) as $p)<li>{{ $p }}</li>@endforeach</ul>
                @endif
            </div>
        @endforeach
    </section>
    @endif

    @if ($proyek->isNotEmpty())
    <section class="bagian">
        <h2>Proyek Pilihan</h2>
        @foreach ($proyek as $p)
            <p class="proyek-baris"><b>{{ $p->title }}.</b> {{ $p->short_description }}@if (! empty($p->tech_stack))<span class="kecil">{{ implode(', ', $p->tech_stack) }}</span>@endif</p>
        @endforeach
        <p class="kecil">{{ max(0, $semuaProyek->count() - $proyek->count()) }} proyek lainnya di {{ Str::of(url('/'))->after('://')->rtrim('/') }}</p>
    </section>
    @endif

    @if ($teknologi->isNotEmpty())
    <section class="bagian">
        <h2>Keahlian</h2>
        <dl class="keahlian">
            @foreach ($teknologi as $kelompok => $isi)
                <dt>{{ $kelompok }}</dt><dd>{{ $isi->pluck('name')->implode(', ') }}</dd>
            @endforeach
            @if ($identity->languages)<dt>Bahasa</dt><dd>{{ $identity->languages }}</dd>@endif
        </dl>
    </section>
    @endif

    @php $pendidikanAda = $pendidikan->isNotEmpty(); @endphp
    @if ($pendidikanAda)
    <section class="bagian">
        <h2>Pendidikan</h2>
        @foreach ($pendidikan as $x)
            <div class="butir">
                <div class="butir__atas">
                    <span class="butir__judul">{{ $x->title }}</span>
                    <span class="butir__waktu">{{ $x->start_date->year }} – {{ $x->is_current || ! $x->end_date ? 'Sekarang' : $x->end_date->year }}</span>
                </div>
                <div class="butir__org">{{ $x->organization }}@if ($x->grade) <span>· IPK {{ $x->grade }}</span>@endif</div>
                @if ($x->link_url)<p class="kecil">{{ $x->link_label ?: 'Publikasi' }}: {{ Str::of($x->link_url)->after('://') }}</p>@endif
            </div>
        @endforeach
    </section>
    @endif
<p class="kecil" style="margin-top:10pt;">Keaslian dokumen: {{ Str::of(url('/'))->after('://')->rtrim('/') }}/verifikasi/{{ \App\Services\PortfolioPdfService::PENANDA_KODE }}</p>
<script>
    // Resume wajib satu halaman: bila isinya melebihi tinggi A4 (dikurangi
    // margin), seluruh dokumen diperkecil secukupnya — tidak pernah diperbesar.
    (function () {
        var mm = 96 / 25.4, tinggi = (297 - 12 - 11) * mm - 4;
        var isi = document.body.scrollHeight;
        if (isi > tinggi) document.body.style.zoom = String(Math.max(0.8, tinggi / isi));
    })();
</script>
</body>
</html>
