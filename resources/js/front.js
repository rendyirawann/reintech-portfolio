/**
 * Halaman depan — menu layar penuh dan pencarian proyek (AJAX).
 * Latar section Pengalaman sepenuhnya CSS (lihat .kontur di front.css).
 */


/* ─────────── menu layar penuh ─────────── */
const tombol = document.getElementById('floating-logo');
const menu = document.getElementById('menu-layar');

if (tombol && menu) {
    let terakhirFokus = null;

    const buka = () => {
        terakhirFokus = document.activeElement;
        menu.hidden = false;
        requestAnimationFrame(() => menu.classList.add('is-on'));
        document.body.classList.add('menu-buka');
        tombol.setAttribute('aria-expanded', 'true');
        tombol.setAttribute('aria-label', 'Tutup menu');
        menu.querySelector('a')?.focus({ preventScroll: true });
    };

    const tutup = () => {
        menu.classList.remove('is-on');
        document.body.classList.remove('menu-buka');
        tombol.setAttribute('aria-expanded', 'false');
        tombol.setAttribute('aria-label', 'Buka menu');
        setTimeout(() => { menu.hidden = true; }, 300);
        terakhirFokus?.focus?.({ preventScroll: true });
    };

    tombol.addEventListener('click', () => (menu.hidden ? buka() : tutup()));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !menu.hidden) tutup(); });
    menu.querySelectorAll('.menu-layar__tautan a, .menu-layar__cta').forEach((a) => a.addEventListener('click', tutup));

    /* pencarian di menu: diteruskan ke kolom cari proyek (AJAX) */
    const kolom = document.getElementById('menu-cari');
    const kirimCari = () => {
        const q = kolom.value.trim();
        tutup();
        window.pfCariProyek?.(q);
        document.getElementById('projects')?.scrollIntoView({ behavior: 'smooth' });
    };
    kolom?.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); kirimCari(); } });
}

/* ─────────── daftar proyek: AJAX, 4 per halaman ─────────── */
const grid = document.getElementById('projects-grid');
const pager = document.getElementById('proyek-pager');
const formCari = document.getElementById('proyek-cari');

if (grid && pager && formCari) {
    const kolom = formCari.querySelector('input[name="q"]');
    const jumlah = document.getElementById('proyek-jumlah');
    let permintaan = null;
    let jeda;

    const muat = async (q, halaman = 1, gulir = false) => {
        permintaan?.abort();
        permintaan = new AbortController();
        grid.classList.add('is-memuat');
        try {
            const url = new URL(grid.dataset.endpoint, location.origin);
            if (q) url.searchParams.set('q', q);
            url.searchParams.set('proyek', String(halaman));
            const r = await fetch(url, { headers: { Accept: 'application/json' }, signal: permintaan.signal });
            if (!r.ok) throw new Error(String(r.status));
            const d = await r.json();
            grid.innerHTML = d.html;       // HTML dari partial Blade kita sendiri (sudah di-escape server)
            pager.innerHTML = d.pager;
            if (jumlah) jumlah.textContent = `${d.total} proyek`;
            if (gulir) document.getElementById('projects')?.scrollIntoView({ behavior: 'smooth' });
        } catch (e) {
            if (e.name !== 'AbortError') grid.insertAdjacentHTML('afterbegin', '<p class="proyek-kosong">Gagal memuat proyek. Coba lagi.</p>');
        } finally {
            grid.classList.remove('is-memuat');
        }
    };

    formCari.addEventListener('submit', (e) => { e.preventDefault(); muat(kolom.value.trim()); });
    kolom.addEventListener('input', () => { clearTimeout(jeda); jeda = setTimeout(() => muat(kolom.value.trim()), 300); });
    pager.addEventListener('click', (e) => {
        const a = e.target.closest('a[data-page]');
        if (!a) return;
        e.preventDefault();
        if (a.getAttribute('aria-disabled') === 'true' || a.getAttribute('aria-current') === 'page') return;
        muat(kolom.value.trim(), Number(a.dataset.page), true);
    });

    window.pfCariProyek = (q) => { kolom.value = q; muat(q); };
}

/* Latar section Pengalaman: animasi kontur pure CSS (front.css) —
   tanpa JavaScript, tanpa pustaka, tanpa kanvas. */
