<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Teks yang bisa disunting untuk bagian ADMIN: halaman login beserta
 * loader-nya, dan kerangka panel admin (merek, logo, kaki).
 *
 * Diadaptasi dari ContentSchema milik portfolio-pm, dengan nilai bawaan atas
 * nama pemilik situs ini. Disimpan sebagai pasangan kunci-nilai di tabel
 * site_settings yang sudah ada, sehingga menambah isian baru cukup dengan
 * menambah satu baris di skema — tanpa migrasi.
 *
 * Isian yang dikosongkan mundur ke nilai bawaan, bukan tampil kosong: halaman
 * login yang judulnya hilang karena isiannya terhapus tidak sengaja jauh
 * lebih buruk daripada teks bawaan.
 */
class AdminContent
{
    private const KUNCI_CACHE = 'admin-content:v1';

    /**
     * Skema per kelompok: kunci => [label, tipe, bawaan].
     *
     * @return array<string, array{judul: string, isian: array<string, array{0: string, 1: string, 2: string|null}>}>
     */
    public static function skema(): array
    {
        return [
            'login' => [
                'judul' => 'Halaman Login & Loader',
                'isian' => [
                    'login_brand_name'    => ['Nama (panel kiri, loader & hak cipta)', 'text', 'Rendy Irawan'],
                    'login_tagline'       => ['Tagline di bawah nama', 'text', 'Portfolio Control Center'],
                    'login_kicker'        => ['Teks kecil di atas judul', 'text', 'Pusat Kendali'],
                    'login_headline_1'    => ['Judul baris 1 (putih)', 'text', 'Bangun'],
                    'login_headline_2'    => ['Judul baris 2 (abu)', 'text', 'Portofolio'],
                    'login_lead'          => ['Deskripsi', 'textarea', 'Semua yang tampil di halaman depan diatur dari sini — dan setiap perubahan bisa dilihat pratinjaunya sebelum disimpan.'],
                    'login_point_1'       => ['Poin 1', 'text', 'Kelola hero, profil, layanan & seluruh konten'],
                    'login_point_2'       => ['Poin 2', 'text', 'Unggah proyek — tempel gambar langsung dari papan klip'],
                    'login_point_3'       => ['Poin 3', 'text', 'Pratinjau langsung & ekspor portofolio PDF'],
                    'login_card_title'    => ['Judul formulir', 'text', 'Selamat datang kembali'],
                    'login_card_subtitle' => ['Subjudul formulir', 'text', 'Masuk untuk melanjutkan ke dashboard.'],
                    'login_button'        => ['Teks tombol', 'text', 'Masuk'],
                    'login_button_busy'   => ['Teks tombol saat memproses', 'text', 'Memverifikasi…'],
                    'login_foot'          => ['Teks di bawah formulir', 'text', 'Kembali ke halaman portofolio'],
                    'login_loader_label'  => ['Loader — nama (kosongkan = nama di atas)', 'text', ''],
                    'login_loader_text'   => ['Loader — teks di bawah bilah', 'text', 'Memuat panel…'],
                    'login_progress_1'    => ['Proses masuk — langkah 1', 'text', 'Memverifikasi kredensial'],
                    'login_progress_2'    => ['Proses masuk — langkah 2', 'text', 'Kredensial terverifikasi'],
                    'login_progress_3'    => ['Proses masuk — langkah 3', 'text', 'Menyiapkan sesi aman'],
                    'login_progress_4'    => ['Proses masuk — langkah 4', 'text', 'Membuka dashboard'],
                ],
            ],
            'admin' => [
                'judul' => 'Navbar & Footer Admin',
                'isian' => [
                    'admin_brand_name'          => ['Nama di navbar admin', 'text', 'Rendy Irawan'],
                    'admin_brand_tagline'       => ['Tagline di navbar admin', 'text', 'Panel Pengelola'],
                    'admin_footer_text'         => ['Nama di hak cipta kaki admin (tahun otomatis)', 'text', 'Rendy Irawan'],
                    'admin_footer_link'         => ['Tautan saat nama di kaki diklik', 'url', ''],
                    'admin_footer_show_socials' => ['Tampilkan ikon media sosial di kaki admin', 'toggle', '1'],
                ],
            ],
        ];
    }

    /** Panduan singkat di atas tiap halaman / tab: apa yang diatur dan di mana tampilnya. */
    public static function panduan(): array
    {
        return [
            'experiences' => [
                'Riwayat kerja, pendidikan, dan organisasi. Tampil sebagai timeline di halaman depan dan dipakai CV & Resume.',
                ['Urutan otomatis: tanggal mulai terbaru di atas.', 'Satu baris di Deskripsi = satu poin pencapaian.', 'Centang "Masih berjalan" untuk posisi saat ini — durasinya dihitung sampai hari ini.'],
            ],
            'tech-stacks' => [
                'Teknologi yang Anda kuasai. Tampil sebagai pita ikon berjalan di halaman depan, dan di Portofolio, CV, serta Resume PDF.',
                ['Kode ikon memakai nama dari simpleicons.org (mis. laravel, postgresql). Kosongkan untuk ditebak dari nama.', 'Warna = warna merek ikon.', 'Sembunyikan untuk menghapus dari tampilan tanpa kehilangan datanya.'],
            ],
            'login' => [
                'Semua teks di halaman login admin, layar loading saat halaman dibuka, dan tahapan proses saat tombol Masuk ditekan.',
                ['Ketik di kolom kiri — pratinjau di kanan langsung berubah.', 'Mengubah teks loader atau langkah proses akan memutar ulang animasinya di pratinjau.', 'Klik Simpan agar berlaku di halaman login sebenarnya.'],
            ],
            'admin' => [
                'Nama, tagline, dan kaki di panel admin ini — bukan situs publik. Ikon di kaki admin diambil dari menu Media Sosial.',
                ['Ubah nama atau tagline, lalu lihat hasilnya di pratinjau.', 'Kosongkan tautan kaki bila nama di hak cipta tidak perlu bisa diklik.'],
            ],
            'identity' => [
                'Identitas situs: logo, teks bilah atas, data diri, dan SEO. Data diri dipakai di bagian Tentang dan di dokumen PDF yang diekspor.',
                ['Isi data diri dan foto profil lebih dulu — dashboard menghitung kelengkapannya.', 'SEO boleh dikosongkan; judul dan deskripsi mundur ke logo dan teks hero.'],
            ],
            'sections' => [
                'Judul, subjudul, dan deskripsi tiap bagian halaman depan, ditambah daftar isinya: statistik hero, kartu Tentang, dan Layanan.',
                ['Setiap bagian punya tombol Simpan sendiri.', 'Pratinjau di kanan mengikuti kolom mana pun yang sedang Anda ketik.'],
            ],
            'projects' => [
                'Proyek yang tampil di bagian Proyek halaman depan dan di dokumen PDF (tiga teratas menurut urutan).',
                ['Gambar utama menjadi sampul kartu proyek.', 'Galeri, berkas, dan tautan bisa diisi sekaligus saat membuat proyek.', 'Gambar boleh ditempel langsung dengan Ctrl+V.'],
            ],
            'socials' => [
                'Tautan media sosial di sidebar situs dan di kaki admin. Ikonnya mengikuti alamatnya sendiri — cukup tempelkan tautannya.',
                ['Ubah label pada baris yang sudah ada — pratinjau di kanan ikut berubah.', 'Tautan baru tampil di situs setelah disimpan.'],
            ],
            'settings' => [
                'Akun Anda untuk masuk ke panel ini: nama, email login, dan kata sandi. Nama tampil di pojok kanan atas panel admin.',
                ['Isi kata sandi saat ini hanya bila ingin menggantinya.', 'Kata sandi baru minimal 6 karakter.'],
            ],
            'export' => [
                'Dokumen PDF dari isi situs Anda: foto, data diri, statistik, layanan, dan tiga proyek teratas — satu halaman per proyek.',
                ['Periksa pratinjaunya di bawah.', 'Buka di tab baru untuk melihat ukuran penuh, atau Unduh PDF untuk menyimpannya.'],
            ],
            'dashboard' => [
                null,
                null,
            ],
        ];
    }

    /** Petunjuk di bawah isian tertentu: contoh, batas, atau di mana ia tampil. */
    public static function petunjuk(): array
    {
        return [
            'login_brand_name'  => 'Tampil di panel kiri halaman login, di layar loading, dan di hak cipta.',
            'login_headline_1'  => 'Singkat: 1–2 kata. Baris kedua tampil lebih redup.',
            'login_lead'        => 'Satu kalimat di bawah judul besar.',
            'login_loader_text' => 'Teks kecil di bawah bilah loading saat halaman login dibuka.',
            'login_progress_1'  => 'Tahapan setelah tombol Masuk ditekan, tampil berurutan.',
            'admin_footer_link' => 'Opsional. Bila diisi, nama di kaki admin bisa diklik.',
        ];
    }

    /** Semua nilai tersimpan, digabung dengan bawaan. Dicache sampai ada yang disimpan. */
    public static function semua(): array
    {
        $bawaan = [];
        foreach (self::skema() as $kelompok) {
            foreach ($kelompok['isian'] as $kunci => [, , $nilai]) {
                $bawaan[$kunci] = (string) ($nilai ?? '');
            }
        }

        try {
            $tersimpan = Cache::rememberForever(self::KUNCI_CACHE, static function () use ($bawaan) {
                // Satu kueri untuk semua kunci — halaman login dan setiap
                // halaman admin membaca ini, jadi tidak boleh satu kueri per isian.
                return Schema::hasTable('site_settings')
                    ? SiteSetting::query()->whereIn('key', array_keys($bawaan))->pluck('value', 'key')->all()
                    : [];
            });
        } catch (Throwable) {
            $tersimpan = [];
        }

        return array_merge($bawaan, array_filter($tersimpan, static fn ($v) => $v !== null));
    }

    public static function lupakanCache(): void
    {
        Cache::forget(self::KUNCI_CACHE);
    }

    /** Teks halaman login yang siap dipakai view. */
    public static function login(): array
    {
        $c = self::semua();
        $bawaan = self::bawaan();
        $ambil = static fn (string $k): string => trim((string) ($c[$k] ?? '')) ?: (string) ($bawaan[$k] ?? '');
        $nama = $ambil('login_brand_name');

        return [
            'name'          => $nama,
            'tagline'       => $ambil('login_tagline'),
            'kicker'        => $ambil('login_kicker'),
            'headline_1'    => $ambil('login_headline_1'),
            'headline_2'    => $ambil('login_headline_2'),
            'lead'          => $ambil('login_lead'),
            'points'        => array_values(array_filter([$ambil('login_point_1'), $ambil('login_point_2'), $ambil('login_point_3')])),
            'card_title'    => $ambil('login_card_title'),
            'card_subtitle' => $ambil('login_card_subtitle'),
            'button'        => $ambil('login_button'),
            'button_busy'   => $ambil('login_button_busy'),
            'foot'          => $ambil('login_foot'),
            'loader_label'  => trim((string) ($c['login_loader_label'] ?? '')) ?: $nama,
            'loader_text'   => $ambil('login_loader_text'),
            'progress'      => array_values(array_filter([
                $ambil('login_progress_1'), $ambil('login_progress_2'), $ambil('login_progress_3'), $ambil('login_progress_4'),
            ])),
        ];
    }

    /** Kerangka panel admin: merek dan kaki. */
    public static function admin(): array
    {
        $c = self::semua();
        $bawaan = self::bawaan();
        $ambil = static fn (string $k): string => trim((string) ($c[$k] ?? '')) ?: (string) ($bawaan[$k] ?? '');

        return [
            'name'          => $ambil('admin_brand_name'),
            'tagline'       => $ambil('admin_brand_tagline'),
            'footer_name'   => $ambil('admin_footer_text'),
            'footer_link'   => trim((string) ($c['admin_footer_link'] ?? '')) ?: null,
            'show_socials'  => ($c['admin_footer_show_socials'] ?? '1') === '1',
        ];
    }

    /** @return array<string, string> */
    private static function bawaan(): array
    {
        $hasil = [];
        foreach (self::skema() as $kelompok) {
            foreach ($kelompok['isian'] as $kunci => [, , $nilai]) {
                $hasil[$kunci] = (string) ($nilai ?? '');
            }
        }

        return $hasil;
    }
}
