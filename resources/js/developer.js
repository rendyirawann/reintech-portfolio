/**
 * Perilaku global panel admin: tema terang/gelap dan menu samping di layar kecil.
 *
 * Ikon tema berupa SVG, bukan emoji — emoji tampil berbeda di setiap sistem
 * operasi dan dilarang sebagai ikon oleh aturan desain proyek ini.
 */

const IKON_MATAHARI =
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">' +
    '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>';

const IKON_BULAN =
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
    '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>';

function simpanTema(tema) {
    try {
        localStorage.setItem('dev-theme', tema);
    } catch {
        // Penyimpanan diblokir (mode privat) — tema tetap berganti, hanya
        // tidak diingat untuk kunjungan berikutnya.
    }
}

function pasangTema() {
    const akar = document.documentElement;
    const tombol = document.getElementById('theme-toggle');
    if (!tombol) return;

    const gambar = () => {
        const gelap = akar.getAttribute('data-dev-theme') === 'dark';
        // Ikon menunjukkan tema YANG AKAN dipilih, bukan yang sedang aktif.
        tombol.innerHTML = gelap ? IKON_MATAHARI : IKON_BULAN;
        tombol.setAttribute('aria-label', gelap ? 'Ganti ke tema terang' : 'Ganti ke tema gelap');
    };

    gambar();

    tombol.addEventListener('click', () => {
        const baru = akar.getAttribute('data-dev-theme') === 'dark' ? 'light' : 'dark';
        akar.setAttribute('data-dev-theme', baru);
        simpanTema(baru);
        gambar();
    });
}

function pasangMenu() {
    const tataLetak = document.getElementById('dev-layout');
    const tombol = document.getElementById('dev-menu-toggle');
    if (!tataLetak || !tombol) return;

    const setel = (buka) => {
        tataLetak.classList.toggle('menu-buka', buka);
        tombol.setAttribute('aria-expanded', String(buka));
    };

    tombol.addEventListener('click', () => setel(!tataLetak.classList.contains('menu-buka')));

    // Ketuk lapisan gelap di luar menu untuk menutupnya.
    tataLetak.addEventListener('click', (e) => {
        if (tataLetak.classList.contains('menu-buka') && e.target === tataLetak) setel(false);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') setel(false);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    pasangTema();
    pasangMenu();
});
