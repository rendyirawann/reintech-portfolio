{{-- Gaya dasar CV & Resume. Satu keluarga huruf, satu warna aksen, garis tipis —
     dokumen yang dibaca perekrut dalam hitungan detik, bukan poster. --}}
<style>
    @if ($font)
    @font-face { font-family: 'Jakarta'; src: url('{{ $font }}') format('woff2'); font-weight: 200 800; font-display: block; }
    @endif
    :root { --tinta: #0f172a; --isi: #334155; --redup: #64748b; --garis: #e2e8f0; --aksen: #1d4ed8; --aksen-muda: #eff4ff; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    body { font-family: 'Jakarta', 'Plus Jakarta Sans', system-ui, sans-serif; color: var(--isi); font-size: 9.6pt; line-height: 1.5; }
    a { color: inherit; text-decoration: none; }
    h1, h2, h3 { color: var(--tinta); }
    .bagian { margin-top: 14pt; }
    .bagian > h2 { font-size: 8.4pt; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--aksen); padding-bottom: 4pt; margin-bottom: 8pt; border-bottom: 1px solid var(--garis); }
    .butir { break-inside: avoid; margin-bottom: 9pt; }
    .butir__atas { display: flex; justify-content: space-between; align-items: baseline; gap: 12pt; }
    .butir__judul { font-size: 10.4pt; font-weight: 700; color: var(--tinta); }
    .butir__waktu { font-size: 8.6pt; color: var(--redup); white-space: nowrap; font-variant-numeric: tabular-nums; }
    .butir__org { font-weight: 600; color: var(--isi); }
    .butir__org span { font-weight: 400; color: var(--redup); }
    .butir ul { margin: 3pt 0 0 12pt; }
    .butir li { margin-bottom: 1.5pt; }
    .butir li::marker { color: var(--aksen); }
    .kecil { font-size: 8.6pt; color: var(--redup); }
</style>
