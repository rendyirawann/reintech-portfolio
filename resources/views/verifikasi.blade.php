@php
    use Illuminate\Support\Str;
    $nama = $identity->full_name ?: 'Pemilik situs';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Verifikasi Dokumen — {{ $nama }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/reintech-logo.svg') }}">
    <style>
        :root { color-scheme: dark; --bg: #05070d; --kartu: #0c111d; --garis: rgba(148,163,184,.16); --teks: #e2e8f0; --redup: #94a3b8; --aksen: #22d3ee; --ok: #10b981; --gagal: #f43f5e; }
        * { box-sizing: border-box; margin: 0; }
        body { min-height: 100vh; display: grid; place-items: center; padding: 32px 20px; background: radial-gradient(60% 50% at 100% 0%, rgba(59,130,246,.16), transparent 70%), var(--bg); color: var(--teks); font: 15px/1.6 system-ui, -apple-system, 'Segoe UI', sans-serif; }
        main { width: min(560px, 100%); }
        .merek { display: flex; align-items: center; gap: 10px; margin-bottom: 22px; color: var(--redup); font-size: 13px; text-decoration: none; }
        .merek img { width: 28px; height: 28px; }
        .kartu { padding: 28px; border-radius: 20px; background: var(--kartu); border: 1px solid var(--garis); }
        h1 { font-size: 22px; margin-bottom: 6px; }
        .sub { color: var(--redup); margin-bottom: 22px; }
        .info { display: grid; grid-template-columns: 130px 1fr; gap: 8px 14px; padding: 16px 0; border-block: 1px solid var(--garis); font-size: 14px; }
        .info dt { color: var(--redup); }
        .info dd { font-weight: 600; word-break: break-all; }
        .hasil { display: flex; gap: 12px; align-items: flex-start; padding: 16px; margin-bottom: 20px; border-radius: 14px; }
        .hasil svg { width: 22px; height: 22px; flex-shrink: 0; margin-top: 2px; }
        .hasil--asli { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.4); }
        .hasil--asli svg { color: var(--ok); }
        .hasil--gagal { background: rgba(244,63,94,.1); border: 1px solid rgba(244,63,94,.4); }
        .hasil--gagal svg { color: var(--gagal); }
        .hasil b { display: block; margin-bottom: 2px; }
        .hasil p { color: var(--redup); font-size: 14px; }
        form { margin-top: 20px; display: grid; gap: 12px; }
        label { font-size: 14px; font-weight: 600; }
        input[type=file] { width: 100%; padding: 14px; border-radius: 12px; border: 1px dashed rgba(148,163,184,.4); background: rgba(255,255,255,.02); color: var(--teks); cursor: pointer; }
        button { padding: 13px 20px; border: 0; border-radius: 100px; background: linear-gradient(100deg, #22d3ee, #3b82f6 55%, #1d4ed8); color: #fff; font: inherit; font-weight: 700; cursor: pointer; transition: opacity .2s ease; }
        button:hover { opacity: .9; }
        .galat { color: var(--gagal); font-size: 14px; }
        .catatan { margin-top: 18px; font-size: 12.5px; color: var(--redup); }
        code { font-size: 12px; color: var(--redup); }
    </style>
</head>
<body>
<main>
    <a class="merek" href="{{ route('home') }}"><img src="{{ asset('images/reintech-logo.svg') }}" alt=""> {{ Str::of(url('/'))->after('://') }}</a>
    <div class="kartu">
        <h1>Verifikasi dokumen</h1>
        <p class="sub">Pastikan PDF milik {{ $nama }} yang Anda terima asli dan belum diubah.</p>

        @if ($hasil === 'asli')
            <div class="hasil hasil--asli" role="status">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                <div><b>Dokumen asli</b><p>Berkas ini identik byte demi byte dengan yang diterbitkan situs ini. Tidak ada satu pun bagian yang diubah.</p></div>
            </div>
        @elseif ($hasil === 'tidak-cocok')
            <div class="hasil hasil--gagal" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
                <div><b>Tidak cocok</b><p>Berkas ini tidak sama dengan dokumen mana pun yang diterbitkan situs ini — kemungkinan telah diubah, atau bukan berasal dari sini.@if ($dokumen) Versi asli untuk kode {{ $kode }} ada di bawah.@endif</p></div>
            </div>
        @endif

        @if ($dokumen)
            <dl class="info">
                <dt>Kode</dt><dd>{{ $dokumen->kode }}</dd>
                <dt>Dokumen</dt><dd>{{ $jenisLabel[$dokumen->jenis] ?? $dokumen->jenis }} — {{ $nama }}</dd>
                <dt>Diterbitkan</dt><dd>{{ $dokumen->created_at->locale('id')->translatedFormat('j F Y, H:i') }} WIB</dd>
                <dt>SHA-256</dt><dd><code>{{ $dokumen->sha256 }}</code></dd>
            </dl>
        @elseif ($kode)
            <div class="hasil hasil--gagal" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>
                <div><b>Kode {{ $kode }} tidak dikenal</b><p>Situs ini tidak pernah menerbitkan dokumen dengan kode tersebut.</p></div>
            </div>
        @endif

        <form action="{{ route('verifikasi.periksa') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($kode)<input type="hidden" name="kode" value="{{ $kode }}">@endif
            <label for="berkas">Periksa berkas PDF yang Anda terima</label>
            <input type="file" id="berkas" name="berkas" accept="application/pdf" required>
            @error('berkas')<p class="galat">{{ $message }}</p>@enderror
            <button type="submit">Periksa keaslian</button>
        </form>
        <p class="catatan">Berkas hanya dihitung sidik jarinya (SHA-256) di server lalu langsung dibuang — tidak disimpan.</p>
    </div>
</main>
</body>
</html>
