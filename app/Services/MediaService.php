<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Satu-satunya jalan masuk berkas unggahan ke penyimpanan.
 *
 * KENAPA TERPUSAT
 *
 * Sebelum ini tiap controller memanggil `$file->store(...)` sendiri-sendiri.
 * Akibatnya aturan ukuran, jenis berkas, dan kompresi tersebar dan tidak pernah
 * sama — dan yang paling berbahaya, nama berkas ikut ditentukan oleh nama
 * kiriman pengguna. Di sini nama SELALU dibangkitkan ulang, sehingga
 * `tembak.php.jpg` maupun `../../rahasia` tidak bisa menjadi nama berkas.
 *
 * KOMPRESI
 *
 * Gambar dikecilkan sampai sisi terpanjang 1920px lalu ditulis ulang sebagai
 * WebP mutu 82. Pada foto layar dan tangkapan layar, itu memangkas 60-90%
 * ukuran tanpa perbedaan yang terlihat mata — dan beda dengan memperkecil
 * dimensi tampilan, hasilnya benar-benar lebih ringan diunduh.
 *
 * Gambar yang SUDAH lebih kecil dari ambang tidak diperbesar. PNG dengan
 * transparansi tetap dipertahankan alfanya.
 *
 * Yang TIDAK dikompresi: SVG (bukan raster, dan menulis ulangnya lewat GD
 * justru merusaknya) dan berkas non-gambar seperti PDF.
 */
class MediaService
{
    /** Sisi terpanjang maksimum setelah kompresi. */
    private const MAKS_SISI = 1920;

    /** Mutu WebP. 82 adalah titik ketika selisihnya tidak lagi terlihat. */
    private const MUTU = 82;

    /** Jenis gambar yang boleh diunggah. */
    public const MIME_GAMBAR = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    /** Jenis berkas pendukung proyek (selain gambar). */
    public const MIME_BERKAS = [
        'application/pdf',
        'application/zip',
        'application/x-zip-compressed',
        'text/plain',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    /**
     * Simpan satu gambar, dikompresi bila memungkinkan.
     *
     * @return string Jalur relatif pada disk `public`.
     */
    public function simpanGambar(UploadedFile $file, string $folder): string
    {
        $mime = $file->getMimeType();
        $nama = Str::uuid()->toString();

        // SVG tidak melewati GD: menulis ulangnya akan merusak isinya. Tetap
        // disimpan apa adanya, dan keamanannya dijaga di sisi lain — berkas
        // diservis dari disk publik tanpa eksekusi PHP.
        if ($mime === 'image/svg+xml') {
            return $file->storeAs($folder, $nama . '.svg', 'public');
        }

        $sumber = $this->bacaGambar($file->getRealPath(), $mime);
        if ($sumber === null) {
            // Gagal dibaca GD -> simpan apa adanya daripada menolak unggahan
            // yang sebenarnya sah.
            return $file->storeAs($folder, $nama . '.' . $file->extension(), 'public');
        }

        // PNG/GIF berpalet (8-bit) ditolak imagewebp(); ubah dulu ke truecolor.
        if (! imageistruecolor($sumber)) {
            imagepalettetotruecolor($sumber);
            imagealphablending($sumber, false);
            imagesavealpha($sumber, true);
        }

        [$lebar, $tinggi] = [imagesx($sumber), imagesy($sumber)];
        $skala = min(1.0, self::MAKS_SISI / max($lebar, $tinggi));

        if ($skala < 1.0) {
            $lebarBaru = (int) round($lebar * $skala);
            $tinggiBaru = (int) round($tinggi * $skala);
            $tujuan = imagecreatetruecolor($lebarBaru, $tinggiBaru);
            imagealphablending($tujuan, false);
            imagesavealpha($tujuan, true);
            imagecopyresampled($tujuan, $sumber, 0, 0, 0, 0, $lebarBaru, $tinggiBaru, $lebar, $tinggi);
            imagedestroy($sumber);
            $sumber = $tujuan;
        }

        $jalur = $folder . '/' . $nama . '.webp';
        $sementara = tempnam(sys_get_temp_dir(), 'img');
        imagewebp($sumber, $sementara, self::MUTU);
        imagedestroy($sumber);

        Storage::disk('public')->put($jalur, file_get_contents($sementara));
        @unlink($sementara);

        return $jalur;
    }

    /** Simpan berkas non-gambar apa adanya, dengan nama yang dibangkitkan ulang. */
    public function simpanBerkas(UploadedFile $file, string $folder): array
    {
        $nama = Str::uuid()->toString() . '.' . $file->extension();

        // Kuncinya 'file_path', sama persis dengan nama kolom di tabel
        // project_files, supaya hasilnya bisa langsung dipakai create().
        return [
            'file_path'     => $file->storeAs($folder, $nama, 'public'),
            'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
            'mime_type'     => $file->getMimeType(),
            'size_bytes'    => $file->getSize(),
        ];
    }

    /**
     * Hapus berkas bila ada. Aman dipanggil untuk jalur kosong.
     *
     * Kegagalan DICATAT, tidak ditelan. Penghapusan bisa gagal karena izin
     * direktori — dan bila itu terjadi diam-diam, berkas yatim menumpuk tanpa
     * ada yang tahu sampai disk penuh, sementara barisnya di basis data sudah
     * hilang sehingga tidak ada lagi yang menunjuk ke sana.
     */
    public function hapus(?string $jalur): bool
    {
        if (blank($jalur) || ! Storage::disk('public')->exists($jalur)) {
            return true;
        }

        $berhasil = Storage::disk('public')->delete($jalur);

        if (! $berhasil) {
            Log::warning('Berkas gagal dihapus dari penyimpanan', ['jalur' => $jalur]);
        }

        return $berhasil;
    }

    private function bacaGambar(string $jalur, ?string $mime)
    {
        $gambar = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($jalur),
            'image/png'  => @imagecreatefrompng($jalur),
            'image/webp' => @imagecreatefromwebp($jalur),
            'image/gif'  => @imagecreatefromgif($jalur),
            default      => false,
        };

        return $gambar ?: null;
    }
}
