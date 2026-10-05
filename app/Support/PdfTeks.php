<?php

namespace App\Support;

/**
 * Merapikan teks KHUSUS dokumen PDF: tanpa tanda hubung/pisah ("-", "–", "—").
 *
 * Data di situs tidak diubah — penyaringan ini hanya dijalankan pada HTML
 * dokumen sebelum dicetak. Yang disentuh hanya teks di antara tag; atribut,
 * <style>, <script>, dan alamat (domain, URL) dibiarkan apa adanya.
 */
class PdfTeks
{
    /** Frasa yang punya padanan tanpa garis. Urutan penting: frasa panjang dulu. */
    private const GANTI = [
        ' — penumpang, driver, dan merchant — ' => ' (penumpang, driver, dan merchant) ',
        ' — perizinan, Mal Pelayanan Publik, dan persetujuan kesesuaian ruang — ' => ' (perizinan, Mal Pelayanan Publik, dan persetujuan kesesuaian ruang) ',
        'berita acara ber-TTE' => 'berita acara bertanda tangan elektronik',
        'ber-TTE' => 'bertanda tangan elektronik',
        'CBT-Sync' => 'CBT Sync', 'Clip-Sync' => 'Clip Sync',
        'real-time' => 'waktu nyata', 'antar-jemput' => 'antar jemput', 'add-on' => 'tambahan',
        'multi-sesi' => 'banyak sesi', 'auto-reply' => 'balasan otomatis', 'ride-hailing' => 'transportasi daring',
        'sub-jenis' => 'subjenis', 'Medan - Deli Serdang' => 'Medan, Deli Serdang',
    ];

    public static function bersihkan(string $html): string
    {
        // Pisahkan blok yang tidak boleh disentuh.
        $bagian = preg_split('#(<(?:style|script)\b.*?</(?:style|script)>|<[^>]+>)#si', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($bagian as $i => $b) {
            if ($b === '' || $b[0] === '<') {
                continue;
            }
            $bagian[$i] = self::teks($b);
        }

        return implode('', $bagian);
    }

    private static function teks(string $t): string
    {
        $t = strtr($t, self::GANTI);
        // Judul "A — B" → "A: B"; sisa tanda pisah di tengah kalimat → koma.
        $t = preg_replace('/^(\s*[^—–\s][^—–]{0,40}?)\s+—\s+/u', '$1: ', $t);
        $t = preg_replace('/\s+[—–]\s+(?=\S)/u', ', ', $t);
        // Rentang: "2024–2025", "Jan 2024 – Des 2025" → "s/d".
        $t = preg_replace('/(\d)\s*[–—]\s*(\p{L}|\d)/u', '$1 s/d $2', $t);
        $t = preg_replace('/(\p{L}{3} \d{4}),\s(?=(?:\p{L}{3} \d{4}|Sekarang))/u', '$1 s/d ', $t);

        return $t;
    }
}
