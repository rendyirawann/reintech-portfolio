/**
 * Kotak unggah: klik, seret-jatuhkan, dan TEMPEL dari papan klip.
 *
 * KENAPA LEWAT DataTransfer
 *
 * Gambar hasil Ctrl+V datang sebagai objek File di dalam ClipboardEvent, bukan
 * sebagai berkas yang dipilih pengguna. Satu-satunya cara memasukkannya ke
 * <input type="file"> — sehingga ikut terkirim oleh pengiriman formulir biasa,
 * tanpa endpoint terpisah dan tanpa kehilangan perlindungan CSRF — adalah
 * membangun DataTransfer baru lalu menyetel `input.files`.
 *
 * Menyimpan berkas di variabel JS lalu mengunggahnya sendiri lewat fetch akan
 * bekerja juga, tetapi berarti dua jalur unggah yang harus dijaga tetap sama
 * aturannya. Satu jalur lebih sedikit yang bisa menyimpang.
 *
 * Dipasang HANYA di halaman yang memuat berkas ini (form proyek).
 */

const MAKS_BYTE = 2 * 1024 * 1024; // sejalan dengan MAKS_KB di controller

function ukuranTerbaca(bytes) {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(0)} KB`;
    return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
}

function pasangKotak(kotak) {
    const input = kotak.querySelector('input[type="file"]');
    const daftar = kotak.querySelector('[data-daftar]');
    const pesan = kotak.querySelector('[data-pesan]');
    if (!input || !daftar) return;

    const terimaGambar = (input.getAttribute('accept') || '').includes('image');

    function gambarUlang() {
        daftar.innerHTML = '';

        [...input.files].forEach((file) => {
            const baris = document.createElement('div');
            baris.className = 'unggah-item';

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.alt = file.name;
                // URL objek dilepas setelah gambar tampil; tanpa ini setiap
                // pratinjau menahan blob-nya di memori sampai halaman ditutup.
                const url = URL.createObjectURL(file);
                img.src = url;
                img.onload = () => URL.revokeObjectURL(url);
                baris.appendChild(img);
            } else {
                const ikon = document.createElement('span');
                ikon.className = 'unggah-ikon';
                ikon.textContent = (file.name.split('.').pop() || '?').toUpperCase();
                baris.appendChild(ikon);
            }

            const teks = document.createElement('div');
            teks.className = 'unggah-teks';
            teks.innerHTML =
                `<strong>${file.name.replace(/</g, '&lt;')}</strong>` +
                `<span>${ukuranTerbaca(file.size)}</span>`;
            baris.appendChild(teks);

            daftar.appendChild(baris);
        });

        if (pesan) {
            pesan.textContent = input.files.length
                ? `${input.files.length} berkas siap diunggah`
                : '';
        }
    }

    /** Tambahkan berkas ke input tanpa membuang yang sudah ada. */
    function tambah(fileBaru) {
        const dt = new DataTransfer();
        [...input.files].forEach((f) => dt.items.add(f));

        let ditolak = 0;
        fileBaru.forEach((f) => {
            if (f.size > MAKS_BYTE) {
                ditolak += 1;
                return;
            }
            dt.items.add(f);
        });

        input.files = dt.files;

        // Menyetel input.files lewat skrip TIDAK memancarkan 'change' dengan
        // sendirinya. Dipancarkan di sini supaya apa pun yang mendengarkan
        // isian ini — termasuk panel pratinjau — ikut tahu ada berkas baru,
        // sama seperti bila berkasnya dipilih lewat dialog biasa. Pendengar
        // 'change' milik kotak ini sendiri yang menggambar ulang daftarnya.
        input.dispatchEvent(new Event('change', { bubbles: true }));

        if (ditolak && pesan) {
            pesan.textContent = `${ditolak} berkas dilewati karena lebih dari 2 MB.`;
        }
    }

    input.addEventListener('change', gambarUlang);

    // --- seret & jatuhkan ---
    ['dragenter', 'dragover'].forEach((ev) =>
        kotak.addEventListener(ev, (e) => {
            e.preventDefault();
            kotak.classList.add('unggah-aktif');
        }),
    );
    ['dragleave', 'drop'].forEach((ev) =>
        kotak.addEventListener(ev, (e) => {
            e.preventDefault();
            kotak.classList.remove('unggah-aktif');
        }),
    );
    kotak.addEventListener('drop', (e) => {
        if (e.dataTransfer?.files?.length) tambah([...e.dataTransfer.files]);
    });

    kotak.addEventListener('click', (e) => {
        if (e.target.closest('input')) return;
        input.click();
    });

    // --- tempel ---
    // Pendengar dipasang pada dokumen: peramban mengirim `paste` ke elemen
    // yang sedang fokus, dan kotak <div> tidak pernah mendapat fokus kecuali
    // dibuat fokusabel. Yang dipakai adalah kotak yang terakhir disentuh.
    kotak.addEventListener('pointerdown', () => {
        document.querySelectorAll('[data-unggah]').forEach((k) => k.classList.remove('unggah-terpilih'));
        kotak.classList.add('unggah-terpilih');
    });

    kotak.__terimaTempel = (files) => {
        if (!terimaGambar) return false;
        tambah(files);
        return true;
    };
}

function pasangTempelGlobal() {
    document.addEventListener('paste', (e) => {
        const item = [...(e.clipboardData?.items || [])].filter((i) => i.type.startsWith('image/'));
        if (!item.length) return;

        const kotakDipilih =
            document.querySelector('[data-unggah].unggah-terpilih') ||
            document.querySelector('[data-unggah][data-tempel-utama]') ||
            document.querySelector('[data-unggah]');

        if (!kotakDipilih?.__terimaTempel) return;

        const files = item
            .map((i) => i.getAsFile())
            .filter(Boolean)
            .map((f, n) =>
                // Tangkapan layar datang bernama "image.png" — diberi nama unik
                // agar beberapa tempelan berturut-turut tidak saling menimpa
                // saat ditampilkan di daftar.
                new File([f], `tempel-${Date.now()}-${n}.${(f.type.split('/')[1] || 'png')}`, { type: f.type }),
            );

        if (kotakDipilih.__terimaTempel(files)) e.preventDefault();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-unggah]').forEach(pasangKotak);
    pasangTempelGlobal();
});
