<?xml version="1.0" encoding="UTF-8"?>
{{--
    Proyek TIDAK punya halaman sendiri — semuanya ditampilkan di satu halaman
    depan lewat modal. Karena itu yang didaftarkan hanya "/" beserta tanggal
    perubahan terbaru; mendaftarkan /project/slug yang tidak ada akan
    menghasilkan deretan 404 di laporan perayapan.
--}}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ \Illuminate\Support\Carbon::parse($terbaru)->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
</urlset>
