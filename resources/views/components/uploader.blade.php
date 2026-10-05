@props([
    'name',                 // nama input, mis. "images[]"
    'accept' => 'image/*',
    'judul' => 'Unggah berkas',
    'keterangan' => 'Klik, seret ke sini, atau tempel dengan Ctrl+V',
    'utama' => false,       // kotak yang menerima tempelan bila belum ada yang disentuh
    'multiple' => true,     // gambar utama hanya menerima satu berkas
])

{{--
    Kotak unggah yang dipakai ulang di seluruh formulir proyek.

    Input berkasnya SUNGGUHAN dan tetap berada di dalam formulir — tempelan
    papan klip dimasukkan ke sana lewat DataTransfer. Dengan begitu
    pengirimannya melewati jalur formulir biasa: token CSRF, aturan validasi,
    dan MediaService yang sama, tanpa endpoint unggah terpisah.
--}}
<div class="unggah-kotak" data-unggah @if ($utama) data-tempel-utama @endif>
    <input type="file" name="{{ $name }}" accept="{{ $accept }}" @if ($multiple) multiple @endif hidden>

    <div class="unggah-ajakan">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <path d="M12 16V4m0 0 4 4m-4-4L8 8" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" stroke-linecap="round" />
        </svg>
        <strong>{{ $judul }}</strong>
        <span>{{ $keterangan }}</span>
        <small>Maksimum 2 MB per berkas. Gambar dikompres otomatis saat disimpan.</small>
    </div>

    <p class="unggah-pesan" data-pesan role="status" aria-live="polite"></p>
    <div class="unggah-daftar" data-daftar></div>
</div>
