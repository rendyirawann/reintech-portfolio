/**
 * Penerima pratinjau langsung — sisi halaman depan.
 *
 * Dimuat HANYA saat halaman dibuka dengan ?pf_preview=1 di dalam panel
 * pratinjau admin. Panel mengirim { type: 'pf:set', key, value }; setiap
 * elemen dengan kunci yang cocok diperbarui seketika:
 *
 *   data-pf="kunci"       isi teks
 *   data-pf-img="kunci"   atribut src gambar
 *   data-pf-bg="kunci"    background-image
 *
 * KEAMANAN
 *
 * Pesan dari origin lain DIABAIKAN. Nilai dipasang lewat textContent, tidak
 * pernah innerHTML, sehingga teks yang diketik di admin tidak bisa menjadi
 * markup. URL gambar hanya diterima bila berskema blob: (gambar yang baru
 * dipilih, belum tersimpan) atau berasal dari origin yang sama.
 */

const ORIGIN = window.location.origin;

function semua(atribut, kunci) {
    return document.querySelectorAll(`[${atribut}="${CSS.escape(kunci)}"]`);
}

function kilat(el) {
    el.classList.remove('pf-flash');
    // Paksa reflow supaya animasi bisa diputar ulang pada elemen yang sama.
    void el.offsetWidth;
    el.classList.add('pf-flash');
}

function urlAman(nilai) {
    if (!nilai) return null;
    if (nilai.startsWith('blob:')) return nilai;
    try {
        const u = new URL(nilai, ORIGIN);
        return u.origin === ORIGIN ? u.href : null;
    } catch {
        return null;
    }
}

function gulirKe(sasaran) {
    const el = sasaran && document.querySelector(sasaran);
    if (el) window.scrollTo(0, el.getBoundingClientRect().top + window.scrollY - 70);
}

window.addEventListener('message', (e) => {
    if (e.origin !== ORIGIN || !e.data || typeof e.data !== 'object') return;
    const d = e.data;

    if (d.type === 'pf:scroll') {
        gulirKe(d.target);
        return;
    }

    if (d.type === 'pf:project') {
        tampilkanDrafProyek(d.data);
        return;
    }

    if (d.type !== 'pf:set' || typeof d.key !== 'string') return;

    const nilai = d.value == null ? '' : String(d.value);
    let pertama = null;

    semua('data-pf', d.key).forEach((el) => {
        el.textContent = nilai;
        pertama ??= el;
        kilat(el);
    });

    const url = urlAman(nilai);

    semua('data-pf-img', d.key).forEach((el) => {
        if (url) {
            el.src = url;
            el.removeAttribute('srcset');
        }
        pertama ??= el;
        kilat(el);
    });

    semua('data-pf-bg', d.key).forEach((el) => {
        if (url) el.style.backgroundImage = `url("${url}")`;
        pertama ??= el;
        kilat(el);
    });

    putarUlang(d.key, nilai);

    // Jaga elemen yang sedang disunting tetap terlihat di layar pratinjau.
    if (pertama && d.focus) {
        const r = pertama.getBoundingClientRect();
        if (r.top < 60 || r.bottom > window.innerHeight) {
            window.scrollTo({ top: r.top + window.scrollY - window.innerHeight / 3, behavior: 'smooth' });
        }
    }
});

/* ─── detail proyek: draf dari formulir proyek dibuka di modal ─── */
function tampilkanDrafProyek(data) {
    if (!data || typeof data !== 'object' || typeof window.pfProyek !== 'function') return;
    const teks = (v) => (v == null ? '' : String(v));
    // Tautan luar hanya http(s); gambar hanya blob: atau se-origin.
    const tautan = (v) => (/^https?:\/\//i.test(teks(v)) ? teks(v) : '');

    window.pfProyek({
        title: teks(data.title),
        category: teks(data.category),
        year: teks(data.year),
        long_description: teks(data.long_description),
        tech_stack: Array.isArray(data.tech_stack) ? data.tech_stack.map(teks).filter(Boolean) : [],
        live_url: tautan(data.live_url),
        repo_url: tautan(data.repo_url),
        main_image: urlAman(teks(data.main_image)),
        images: Array.isArray(data.images) ? data.images.map((u) => urlAman(teks(u))).filter(Boolean) : [],
    });
}

/* ─── loader & overlay proses: sekali tampil, jadi diputar ulang saat disunting ─── */
const tahanan = {};

function tampilkanSebentar(id, pasang, lepas) {
    const el = document.getElementById(id);
    if (!el) return;
    pasang(el);
    clearTimeout(tahanan[id]);
    tahanan[id] = setTimeout(() => lepas(el), 2600);
}

function putarUlang(kunci, nilai) {
    if (kunci.startsWith('login_loader')) {
        tampilkanSebentar('auth-preloader', (el) => el.classList.remove('is-done'), (el) => el.classList.add('is-done'));
    } else if (kunci.startsWith('login_progress')) {
        tampilkanSebentar('auth-progress', (el) => {
            const langkah = el.querySelector('[data-progress-step]');
            if (langkah) langkah.textContent = nilai;
            el.classList.add('is-on');
        }, (el) => el.classList.remove('is-on'));
    }
}

// Pratinjau hanya untuk dilihat: formulir di dalamnya tidak boleh terkirim
// (misalnya login sungguhan, atau simpan dari panel admin di dalam bingkai).
document.addEventListener('submit', (e) => e.preventDefault(), true);

// Beri tahu panel admin bahwa halaman siap menerima isi formulir saat ini.
if (window.parent !== window) {
    window.parent.postMessage({ type: 'pf:ready' }, ORIGIN);
}
