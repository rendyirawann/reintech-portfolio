/**
 * Pengirim pratinjau langsung — sisi admin.
 *
 * Setiap isian yang diketik diteruskan ke iframe pratinjau sebagai
 * { type: 'pf:set', key, value }. Kuncinya:
 *
 *   data-preview-key pada isian itu sendiri, bila ada; kalau tidak,
 *   data-preview-prefix pada formulir terdekat + nama isian.
 *
 * KENAPA PENDENGARNYA DI TINGKAT DOKUMEN
 *
 * Versi portfolio-pm mengikat SATU formulir. Di sini halaman Bagian
 * memuat belasan formulir sekaligus — satu per bagian, satu per baris
 * statistik, kartu, dan layanan — jadi setiap formulir menyatakan awalannya
 * sendiri dan satu pendengar di dokumen melayani semuanya.
 *
 * Gambar yang dipilih atau DITEMPEL dikirim sebagai URL blob:, sehingga
 * tampil di pratinjau sebelum formulirnya disimpan.
 */

const panel = document.querySelector('[data-pf-preview]');

if (panel) {
    const bingkai = panel.querySelector('[data-frame]');
    const panggung = panel.querySelector('[data-stage]');
    const sasaran = panel.dataset.target || '';
    const ORIGIN = window.location.origin;

    let perangkat = 'desktop';
    let siap = false;

    // SEMUA nilai yang pernah dikirim disimpan, bukan hanya yang tertunda:
    // saat pratinjau dimuat ulang, isinya kembali ke versi tersimpan, dan
    // tanpa ini perubahan yang belum disimpan hilang dari layar pratinjau.
    const terkirim = new Map();

    /* ─── skala: halaman selebar desktop diperkecil agar muat di panel ─── */
    const tata = () => {
        const lebar = perangkat === 'mobile' ? 390 : 1280;
        const skala = Math.min(1, panggung.clientWidth / lebar);
        bingkai.style.width = `${lebar}px`;
        bingkai.style.height = `${Math.round(panggung.clientHeight / skala)}px`;
        bingkai.style.transform = `scale(${skala})`;
        panggung.classList.toggle('is-mobile', perangkat === 'mobile');
    };

    new ResizeObserver(tata).observe(panggung);
    tata();

    panel.querySelectorAll('[data-device]').forEach((tombol) => {
        tombol.addEventListener('click', () => {
            perangkat = tombol.dataset.device;
            panel.querySelectorAll('[data-device]').forEach((t) => {
                const aktif = t === tombol;
                t.classList.toggle('is-active', aktif);
                t.setAttribute('aria-pressed', String(aktif));
            });
            tata();
        });
    });

    panel.querySelector('[data-preview-reload]')?.addEventListener('click', () => {
        siap = false;
        bingkai.contentWindow?.location.reload();
    });

    /* ─── pesan ─── */
    const kirimSatu = (pesan) => bingkai.contentWindow?.postMessage(pesan, ORIGIN);

    const kirim = (kunci, nilai, fokus) => {
        const pesan = { type: 'pf:set', key: kunci, value: nilai, focus: !!fokus };
        terkirim.set(kunci, pesan);
        if (siap) kirimSatu(pesan);
    };

    window.addEventListener('message', (e) => {
        if (e.origin !== ORIGIN || e.source !== bingkai.contentWindow) return;
        if (!e.data || e.data.type !== 'pf:ready') return;

        siap = true;
        if (sasaran) kirimSatu({ type: 'pf:scroll', target: sasaran });
        // Kirim ulang semuanya tanpa fokus, supaya pratinjau tidak melompat-lompat.
        terkirim.forEach((pesan) => kirimSatu({ ...pesan, focus: false }));
    });

    /* ─── detail proyek ─── */
    // Halaman formulir proyek menandai wadahnya dengan data-preview-project.
    // Seluruh draf — teks, gambar utama, galeri tersimpan + yang baru dipilih
    // atau ditempel — dikirim sekaligus dan dibuka sebagai modal detail.
    const wadahProyek = document.querySelector('[data-preview-project]');
    const blobBerkas = new WeakMap();
    const blobDari = (berkas) => {
        if (!blobBerkas.has(berkas)) blobBerkas.set(berkas, URL.createObjectURL(berkas));
        return blobBerkas.get(berkas);
    };

    const drafProyek = () => {
        const ambil = (nama) => wadahProyek.querySelector(`[name="${nama}"]`);
        const nilai = (nama) => ambil(nama)?.value ?? '';
        const gambarBaru = (nama) => [...(ambil(nama)?.files ?? [])].filter((b) => b.type.startsWith('image/')).map(blobDari);

        const utamaBaru = gambarBaru('main_image')[0];
        const utamaLama = wadahProyek.querySelector('[data-pf-main-lama]')?.getAttribute('src');
        const galeriLama = [...wadahProyek.querySelectorAll('[data-pf-galeri-lama]')].map((img) => img.getAttribute('src'));

        return {
            title: nilai('title'),
            category: nilai('category'),
            year: nilai('year'),
            long_description: nilai('long_description') || nilai('short_description'),
            tech_stack: nilai('tech_stack').split(',').map((t) => t.trim()).filter(Boolean),
            live_url: nilai('live_url'),
            repo_url: nilai('repo_url'),
            main_image: utamaBaru || utamaLama || '',
            images: [...galeriLama, ...gambarBaru('images[]')],
        };
    };

    let jedaProyek;
    const kirimProyek = () => {
        clearTimeout(jedaProyek);
        jedaProyek = setTimeout(() => {
            const pesan = { type: 'pf:project', data: drafProyek() };
            terkirim.set('__proyek', pesan);
            if (siap) kirimSatu(pesan);
        }, 150);
    };

    if (wadahProyek) {
        wadahProyek.addEventListener('input', kirimProyek);
        wadahProyek.addEventListener('change', kirimProyek);
        kirimProyek();
    }

    /* ─── kunci sebuah isian ─── */
    const kunciDari = (el) => {
        if (el.dataset.previewKey) return el.dataset.previewKey;

        // Nama majemuk (link_url[], images[]) tidak dipratinjau sebagai teks.
        const nama = (el.name || '').replace(/\[\]$/, '');
        if (!/^[a-z0-9_]+$/i.test(nama)) return null;

        // Atributnya yang menentukan ikut-tidaknya sebuah formulir, bukan
        // isinya: awalan KOSONG itu sah (kuncinya = nama isian apa adanya),
        // sedangkan formulir tanpa atribut ini sama sekali tidak dipratinjau.
        const wadah = el.closest('[data-preview-prefix]');
        return wadah ? (wadah.dataset.previewPrefix || '').trim() + nama : null;
    };

    document.addEventListener('input', (e) => {
        const el = e.target;
        if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement || el instanceof HTMLSelectElement)) return;
        if (el.type === 'file' || el.type === 'hidden' || el.type === 'password') return;

        const kunci = kunciDari(el);
        if (kunci) kirim(kunci, el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value, true);
    });

    // Berkas: dipicu oleh pilih-berkas biasa MAUPUN oleh kotak unggah
    // (seret, tempel), yang memancarkan 'change' setelah menyetel input.files.
    document.addEventListener('change', (e) => {
        const el = e.target;
        if (!(el instanceof HTMLInputElement) || el.type !== 'file') return;

        const berkas = el.files?.[0];
        if (!berkas || !berkas.type.startsWith('image/')) return;

        const kunci = kunciDari(el);
        if (kunci) kirim(kunci, URL.createObjectURL(berkas), true);
    });
}
