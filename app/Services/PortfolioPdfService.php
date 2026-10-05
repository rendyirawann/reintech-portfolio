<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HeroStat;
use App\Models\PortfolioIdentity;
use App\Models\PortfolioSection;
use App\Models\ServiceItem;
use App\Models\Project;
use App\Models\SocialLink;
use App\Models\Experience;
use App\Models\TechStack;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Membuat dokumen portofolio PDF dari isi situs.
 *
 * KENAPA CHROMIUM, BUKAN DOMPDF
 *
 * Dompdf tidak mendukung flexbox maupun grid, sehingga tata letak dua kolom
 * yang rapi harus diakali dengan tabel. Chromium tanpa tampilan sudah
 * terpasang di server ini dan merender CSS persis seperti peramban — hasilnya
 * sama dengan pratinjau cetak, tanpa menambah satu pun dependensi Composer.
 *
 * KENAPA SEMUA ASET DITANAM SEBAGAI DATA URI
 *
 * Chromium dijalankan tanpa kuki sesi dan tanpa akses ke TLS depan. Kalau
 * dokumennya memuat gambar dan font lewat URL, perenderan bergantung pada
 * jaringan saat itu — dan satu gambar yang gagal dimuat menghasilkan PDF
 * berlubang tanpa pesan galat. Dengan data URI, dokumennya mandiri.
 */
class PortfolioPdfService
{
    /** Proyek pilihan di Resume (satu halaman). Portofolio memuat semuanya. */
    public const JUMLAH_PROYEK = 3;

    /**
     * Tiga dokumen berbeda dari data yang sama:
     *  portfolio — visual, studi kasus proyek bergambar;
     *  cv        — lengkap, beberapa halaman: seluruh riwayat, pendidikan, publikasi, semua proyek;
     *  resume    — satu halaman, ringkas, satu kolom (ramah ATS).
     */
    public const JENIS = [
        'portfolio' => 'Portofolio (Bergambar)',
        'portfolio-list' => 'Portofolio (Daftar)',
        'cv' => 'CV',
        'resume' => 'Resume',
    ];

    /** Batas waktu perenderan. Chromium yang macet tidak boleh menahan PHP-FPM. */
    private const BATAS_DETIK = 45;

    /**
     * Aturan pemisah halaman untuk SEMUA dokumen: blok isi (paragraf, butir,
     * baris tabel, gambar, kartu) tidak boleh terbelah dua halaman — bila tak
     * muat, blok itu pindah utuh ke halaman berikutnya. Judul tidak boleh
     * tertinggal sendirian di dasar halaman.
     */
    private const ANTI_POTONG = '<style>
        p, li, tr, img, figure, dl, blockquote, pre, table thead,
        .butir, .publikasi, .proyek-baris, .keahlian, .data,
        [class*="kartu"], [class*="blok"], [class*="card"], .galeri > *, .layanan > *, .indeks li
            { break-inside: avoid; page-break-inside: avoid; }
        h1, h2, h3, h4, .judul-bagian, .butir__atas, .bagian > h2
            { break-after: avoid; page-break-after: avoid; }
        p, li { orphans: 3; widows: 3; }
        /* Paragraf isi rata kiri-kanan; baris terakhir tetap rata kiri.
           Judul, label, chip, dan baris kontak tidak ikut. */
        .ringkasan, .ringkas-proyek, .uraian, .daftar-uraian, .proyek-baris,
        .profil p:not(.headline), .bagian.profil p, .butir li, .layanan span, .publikasi
            { text-align: justify; text-justify: inter-word; text-align-last: left; hyphens: manual; }
    </style>';

    /**
     * PDF dari cache bila isinya sama persis dengan render terakhir.
     *
     * Kuncinya sidik jari HTML dokumen (data, gambar, dan templat sekaligus),
     * jadi cache tidak pernah basi: begitu satu huruf data berubah, kuncinya
     * berubah dan PDF dirender ulang. Pratinjau, "buka di tab baru", dan
     * unduhan untuk data yang sama hanya memanggil Chromium sekali.
     */
    /** Penanda tempat kode verifikasi di templat; diganti setelah kodenya dihitung. */
    public const PENANDA_KODE = '%%KODE-SEGEL%%';

    /**
     * PDF final: dirender, DISEGEL, dan dicatat sidik jarinya.
     *
     * Kode verifikasi = sidik jari isi dokumen (sebelum kode disisipkan), jadi
     * satu versi data = satu kode. Hasil akhirnya di-cache per kode: pratinjau,
     * tab baru, dan unduhan untuk data yang sama memakai berkas yang PERSIS
     * sama — sehingga sidik jari yang dicatat cocok dengan semua salinan.
     */
    public function buat(string $jenis = 'portfolio'): string
    {
        $html = str_replace('</head>', self::ANTI_POTONG . '</head>', $this->html($jenis));
        $kode = strtoupper(substr(hash('sha256', $jenis . '|' . $html), 0, 10));
        $html = str_replace(self::PENANDA_KODE, $kode, $html);

        $dir = storage_path('app/pdf-cache');
        $berkas = $dir . '/' . $jenis . '-' . $kode . '.pdf';
        if (is_file($berkas) && filesize($berkas) > 0) {
            return file_get_contents($berkas);
        }

        $pdf = $this->segel($this->render($html));

        \App\Models\DokumenSegel::updateOrCreate(['kode' => $kode], [
            'jenis' => $jenis,
            'sha256' => hash('sha256', $pdf),
            'ukuran' => strlen($pdf),
        ]);

        if (is_dir($dir) || @mkdir($dir, 0775, true)) {
            foreach (glob($dir . '/' . $jenis . '-*.pdf') ?: [] as $lama) {
                @unlink($lama);
            }
            @file_put_contents($berkas, $pdf);
        }

        return $pdf;
    }

    /**
     * Enkripsi AES-256 dengan izin terbatas: boleh dibuka tanpa kata sandi,
     * dicetak, dan teksnya disalin (sistem ATS perlu membaca teks), tetapi
     * TIDAK boleh diubah, dianotasi, diisi form, atau disusun ulang halamannya.
     * Kata sandi pemilik acak dan dibuang — dokumen tidak pernah perlu diedit;
     * versi baru selalu dibuat ulang dari data situs.
     */
    private function segel(string $pdf): string
    {
        $qpdf = storage_path('app/bin/qpdf/bin/qpdf');
        if (! is_executable($qpdf)) {
            Log::warning('qpdf tidak ditemukan; PDF dikirim tanpa segel.');
            return $pdf;
        }

        $kerja = storage_path('app/pdf-export/segel-' . Str::uuid());
        @mkdir($kerja, 0775, true);
        file_put_contents("$kerja/masuk.pdf", $pdf);

        try {
            $proses = new Process([
                $qpdf, '--encrypt', '--user-password=', '--owner-password=' . bin2hex(random_bytes(24)), '--bits=256',
                '--print=full', '--extract=y', '--modify=none', '--annotate=n', '--form=n', '--assemble=n',
                '--', "$kerja/masuk.pdf", "$kerja/keluar.pdf",
            ]);
            $proses->setTimeout(30);
            $proses->run();

            if (! is_file("$kerja/keluar.pdf") || filesize("$kerja/keluar.pdf") === 0) {
                Log::error('Segel PDF gagal', ['galat' => Str::limit($proses->getErrorOutput(), 500)]);
                throw new RuntimeException('PDF gagal disegel. Rincian dicatat di log.');
            }

            return file_get_contents("$kerja/keluar.pdf");
        } finally {
            $this->bersihkan($kerja);
        }
    }

    private function render(string $html): string
    {
        $kerja = storage_path('app/pdf-export/' . Str::uuid());
        if (! is_dir($kerja) && ! mkdir($kerja, 0775, true) && ! is_dir($kerja)) {
            throw new RuntimeException('Direktori kerja PDF tidak dapat dibuat.');
        }

        $masuk = $kerja . '/portofolio.html';
        $keluar = $kerja . '/portofolio.pdf';
        file_put_contents($masuk, $html);

        try {
            $proses = new Process([
                $this->chromium(),
                '--headless=new',
                '--no-sandbox',
                '--disable-gpu',
                '--disable-dev-shm-usage',
                '--no-pdf-header-footer',
                // Direktori profil sendiri per perenderan: PHP-FPM berjalan
                // sebagai www-data yang tidak punya $HOME yang bisa ditulisi,
                // dan tanpa ini Chromium gagal diam-diam.
                '--user-data-dir=' . $kerja . '/profil',
                '--print-to-pdf=' . $keluar,
                'file://' . $masuk,
            ], null, ['HOME' => $kerja]);

            $proses->setTimeout(self::BATAS_DETIK);
            $proses->run();

            if (! is_file($keluar) || filesize($keluar) === 0) {
                Log::error('Ekspor PDF gagal', [
                    'kode' => $proses->getExitCode(),
                    'galat' => Str::limit($proses->getErrorOutput(), 800),
                ]);

                throw new RuntimeException('PDF gagal dibuat. Rincian galat dicatat di log.');
            }

            return file_get_contents($keluar);
        } finally {
            $this->bersihkan($kerja);
        }
    }

    /** HTML dokumen — terpisah supaya bisa dipratinjau di peramban. */
    public function html(string $jenis = 'portfolio'): string
    {
        return \App\Support\PdfTeks::bersihkan($this->htmlMentah($jenis));
    }

    private function htmlMentah(string $jenis): string
    {
        return match ($jenis) {
            'cv', 'resume' => $this->htmlRiwayat($jenis),
            'portfolio-list' => $this->htmlPortofolio(daftar: true),
            default => $this->htmlPortofolio(),
        };
    }

    private function htmlRiwayat(string $jenis): string
    {
        $identity = PortfolioIdentity::instance();
        $semuaProyek = Project::visible()->ordered()
            ->get(['id', 'title', 'category', 'year', 'short_description', 'tech_stack', 'repo_url', 'live_url']);
        $riwayat = Experience::visible()->ordered()->get();

        return view('export.' . $jenis, [
            'identity' => $identity,
            'kerja' => $riwayat->where('type', 'work')->values(),
            'pendidikan' => $riwayat->where('type', 'education')->values(),
            'organisasi' => $riwayat->where('type', 'organization')->values(),
            'proyek' => $jenis === 'resume' ? $semuaProyek->take(self::JUMLAH_PROYEK) : $semuaProyek,
            'teknologi' => TechStack::visible()->ordered()->get(['name', 'group'])->groupBy('group'),
            'socials' => SocialLink::visible()->ordered()->get(['platform', 'label', 'url']),
            'stats' => HeroStat::visible()->ordered()->get(),
            'semuaProyek' => $semuaProyek,
            'foto' => $this->dataUri($identity->profile_image),
            'font' => $this->fontDataUri(),
            'logo' => $this->logoDataUri(),
        ])->render();
    }

    /** $daftar: halaman proyek berupa daftar tanpa foto (gambar tidak dibaca sama sekali). */
    private function htmlPortofolio(bool $daftar = false): string
    {
        $identity = PortfolioIdentity::instance();

        $projects = Project::query()
            ->visible()
            ->ordered()
            ->select(['id', 'title', 'short_description', 'long_description', 'main_image', 'category', 'year', 'tech_stack', 'live_url', 'repo_url'])
            ->with([
                'links:id,project_id,label,url,platform',
                // Tiga gambar galeri per proyek. Sejak Laravel 11, limit() di
                // dalam eager-load berlaku PER induk (lewat window function),
                // jadi baris selebihnya tidak pernah dibaca dari basis data.
                'images' => fn ($q) => $q->select(['id', 'project_id', 'image_path', 'sort_order'])->limit(3),
            ])
            ->get();

        $socials = SocialLink::visible()->ordered()->get(['platform', 'label', 'url']);
        $stats = HeroStat::visible()->ordered()->get();
        $layanan = ServiceItem::visible()->ordered()->get(['title', 'description']);
        $bagian = PortfolioSection::query()->get(['section_key', 'title', 'subtitle', 'description'])->keyBy('section_key');

        return view('export.portfolio', [
            'identity' => $identity,
            'projects' => $projects,
            'socials' => $socials,
            'stats' => $stats,
            'layanan' => $layanan,
            'bagian' => $bagian,
            'modeDaftar' => $daftar,
            'galeri' => $daftar ? collect() : $projects->mapWithKeys(fn (Project $p) => [
                $p->id => $p->images->take(3)->map(fn ($img) => $this->dataUri($img->image_path))->filter()->values(),
            ]),
            'foto' => $this->dataUri($identity->profile_image),
            'gambarProyek' => $daftar ? collect() : $projects->mapWithKeys(fn (Project $p) => [$p->id => $this->dataUri($p->main_image)]),
            'font' => $this->fontDataUri(),
            'logo' => $this->logoDataUri(),
            'teknologiKelola' => TechStack::visible()->ordered()->pluck('name'),
        ])->render();
    }

    public function namaBerkas(string $jenis = 'portfolio'): string
    {
        $identity = PortfolioIdentity::instance();
        $nama = Str::slug($identity->full_name ?: ($identity->logo_text ?: 'dokumen'));
        $awal = ['portfolio' => 'portofolio', 'portfolio-list' => 'portofolio-daftar', 'cv' => 'cv', 'resume' => 'resume'][$jenis] ?? 'portofolio';

        return $awal . '-' . $nama . '-' . now()->format('Y-m-d') . '.pdf';
    }

    private function dataUri(?string $jalur): ?string
    {
        if (blank($jalur) || ! Storage::disk('public')->exists($jalur)) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($jalur) ?: 'image/webp';

        return 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($jalur));
    }

    private function fontDataUri(): string
    {
        $jalur = resource_path('fonts/PlusJakartaSans-Variable.woff2');

        return is_file($jalur)
            ? 'data:font/woff2;base64,' . base64_encode(file_get_contents($jalur))
            : '';
    }

    private function logoDataUri(): string
    {
        $jalur = public_path('images/reintech-logo.svg');

        return is_file($jalur)
            ? 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($jalur))
            : '';
    }

    private function chromium(): string
    {
        foreach (['/usr/bin/chromium', '/usr/bin/chromium-browser', '/usr/bin/google-chrome'] as $bin) {
            if (is_executable($bin)) {
                return $bin;
            }
        }

        throw new RuntimeException('Chromium tidak ditemukan di server; ekspor PDF tidak dapat dijalankan.');
    }

    private function bersihkan(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $isi = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($isi as $f) {
            $f->isDir() ? @rmdir($f->getPathname()) : @unlink($f->getPathname());
        }

        @rmdir($dir);
    }
}
