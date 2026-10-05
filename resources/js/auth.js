/**
 * Halaman masuk: preloader, latar topologi beranimasi, akun tersimpan,
 * pengiriman lewat fetch, dan serah-terima ke dashboard.
 *
 * Diadaptasi dari app-auth.js milik portfolio-pm. Formulirnya tetap bekerja
 * tanpa skrip ini — tombol Masuk mengirim POST biasa dan server menjawab
 * dengan redirect. Skrip ini hanya menambahkan pengalaman di atasnya.
 */

const PRELOAD_MS = 1200;

/** Kunci localStorage berisi alamat email yang pernah dipakai di perangkat ini. */
const KUNCI_AKUN = 'rt:akun';
const MAKS_AKUN = 4;

const P5_URL = 'https://cdn.jsdelivr.net/npm/p5@1.9.0/lib/p5.min.js';
const VANTA_URL = 'https://cdn.jsdelivr.net/npm/vanta@0.5.24/dist/vanta.topology.min.js';

let kurangiGerak = false;
try {
    kurangiGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
} catch {
    kurangiGerak = false;
}

/* ─────────────── preloader: selalu tampil tepat PRELOAD_MS ─────────────── */

function pasangPreloader() {
    const el = document.getElementById('auth-preloader');
    if (!el) return;

    const mulai = window.__authPreloadStart || Date.now();
    const sisa = Math.max(0, PRELOAD_MS - (Date.now() - mulai));

    window.setTimeout(() => {
        el.classList.add('is-done');
        window.setTimeout(() => el.remove(), kurangiGerak ? 0 : 520);
    }, kurangiGerak ? 0 : sisa);
}

/* ─────────────── latar topologi (Vanta + p5) ───────────────
   Dimuat dari CDN SETELAH halaman tampil, tidak pernah menahan cat pertama.
   Kegagalan apa pun — luring, CDN terblokir, CSP — membiarkan latar diam
   tetap di tempatnya, jadi halamannya tidak pernah lebih buruk karena mencoba. */

function muatSkrip(src) {
    return new Promise((selesai, gagal) => {
        const tag = document.createElement('script');
        tag.src = src;
        tag.async = true;
        tag.crossOrigin = 'anonymous';
        tag.onload = selesai;
        tag.onerror = () => gagal(new Error(`gagal memuat ${src}`));
        document.head.appendChild(tag);
    });
}

/**
 * Latar bergeser berlawanan arah kursor.
 *
 * Efek topologi Vanta sendiri mengabaikan kursor — putaran gambarnya hanya
 * menjalankan partikel melalui medan aliran yang dibuat sekali di awal. Jadi
 * respons terhadap kursor dibuat di sini: kanvas dirender sedikit lebih besar
 * dari layar lalu didorong berlawanan arah, terbaca sebagai kedalaman.
 */
function pasangParalaks(host) {
    if (kurangiGerak) return;

    const MAKS = 26;
    const LUNAK = 0.08;
    let tujuanX = 0, tujuanY = 0, kiniX = 0, kiniY = 0, jalan = false;

    const langkah = () => {
        kiniX += (tujuanX - kiniX) * LUNAK;
        kiniY += (tujuanY - kiniY) * LUNAK;
        host.style.transform = `translate3d(${(kiniX * -MAKS).toFixed(2)}px,${(kiniY * -MAKS).toFixed(2)}px,0)`;

        if (Math.abs(tujuanX - kiniX) > 0.001 || Math.abs(tujuanY - kiniY) > 0.001) {
            window.requestAnimationFrame(langkah);
        } else {
            jalan = false;
        }
    };

    window.addEventListener('mousemove', (e) => {
        tujuanX = (e.clientX / Math.max(1, window.innerWidth)) * 2 - 1;
        tujuanY = (e.clientY / Math.max(1, window.innerHeight)) * 2 - 1;
        if (!jalan) {
            jalan = true;
            window.requestAnimationFrame(langkah);
        }
    }, { passive: true });
}

function pasangVanta() {
    const host = document.getElementById('auth-vanta');
    if (!host || kurangiGerak) return;

    // Di ponsel, kanvas layar penuh yang terus melukis ulang memboroskan
    // baterai untuk latar yang hanya dilihat beberapa detik.
    if (window.innerWidth < 768) return;

    // Unduhan dimulai segera (p5 ±1 MB), tetapi kanvasnya baru ditampilkan
    // setelah preloader hilang, sehingga keduanya tidak pernah bertumpuk.
    const tampilPada = (window.__authPreloadStart || Date.now()) + PRELOAD_MS + 120;

    muatSkrip(P5_URL)
        .then(() => muatSkrip(VANTA_URL))
        .then(() => {
            if (!window.VANTA?.TOPOLOGY || !window.p5) return;
            const terang = document.documentElement.getAttribute('data-dev-theme') === 'light';

            window.__vantaEffect = window.VANTA.TOPOLOGY({
                el: host,
                p5: window.p5,
                mouseControls: false,
                touchControls: false,
                gyroControls: false,
                minHeight: 200,
                minWidth: 200,
                scale: 1,
                scaleMobile: 1,
                color: terang ? (host.dataset.colorLight || '#0891b2') : (host.dataset.color || '#22d3ee'),
                backgroundColor: terang ? (host.dataset.bgLight || '#f4f7fb') : (host.dataset.bg || '#05070d'),
            });

            window.setTimeout(() => {
                host.classList.add('is-on');
                pasangParalaks(host);
            }, Math.max(0, tampilPada - Date.now()));
        })
        .catch(() => {
            /* Latar diam tetap — tidak ada yang perlu dilakukan. */
        });
}

/* ─────────────── akun tersimpan ───────────────
   Yang disimpan HANYA alamat email, dan hanya di perangkat ini. Kata sandi
   tidak pernah ditulis ke sini — itu urusan pengelola sandi peramban. */

function bacaAkun() {
    try {
        const daftar = JSON.parse(window.localStorage.getItem(KUNCI_AKUN) || '[]');
        return Array.isArray(daftar)
            ? daftar.filter((a) => a && typeof a.id === 'string' && a.id.length < 200)
            : [];
    } catch {
        return [];
    }
}

function tulisAkun(daftar) {
    try {
        window.localStorage.setItem(KUNCI_AKUN, JSON.stringify(daftar.slice(0, MAKS_AKUN)));
    } catch {
        /* Mode privat atau penyimpanan penuh — daftarnya tetap kosong. */
    }
}

function ingatAkun(id) {
    const bersih = String(id || '').trim();
    if (!bersih) return;
    const daftar = bacaAkun().filter((a) => a.id.toLowerCase() !== bersih.toLowerCase());
    daftar.unshift({ id: bersih, at: Date.now() });
    tulisAkun(daftar);
}

function inisial(id) {
    const nama = String(id).split('@')[0];
    const bagian = nama.split(/[.\-_\s]+/).filter(Boolean);
    return bagian.length >= 2
        ? (bagian[0][0] + bagian[1][0]).toUpperCase()
        : nama.slice(0, 2).toUpperCase() || '?';
}

function pasangAkun() {
    const wadah = document.getElementById('auth-accounts');
    const input = document.getElementById('auth-identifier');
    if (!wadah || !input) return;

    const daftarEl = wadah.querySelector('[data-accounts-list]');
    const tombolHapusSemua = wadah.querySelector('[data-accounts-clear]');

    const gambar = () => {
        const akun = bacaAkun();
        daftarEl.textContent = '';
        wadah.hidden = akun.length === 0;

        akun.forEach((a) => {
            const li = document.createElement('li');

            const tombol = document.createElement('button');
            tombol.type = 'button';
            tombol.className = 'auth-account';
            tombol.setAttribute('aria-label', `Masuk sebagai ${a.id}`);

            const tanda = document.createElement('span');
            tanda.className = 'auth-account__mark';
            tanda.setAttribute('aria-hidden', 'true');
            tanda.textContent = inisial(a.id);

            const nama = document.createElement('span');
            nama.className = 'auth-account__name';
            // textContent, bukan innerHTML: isinya berasal dari masukan
            // pengguna yang tersimpan di perangkat.
            nama.textContent = a.id;

            tombol.append(tanda, nama);
            tombol.addEventListener('click', () => {
                input.value = a.id;
                document.getElementById('auth-password')?.focus();
            });

            const hapus = document.createElement('button');
            hapus.type = 'button';
            hapus.className = 'auth-account__remove';
            hapus.setAttribute('aria-label', `Lupakan akun ${a.id}`);
            hapus.textContent = '×';
            hapus.addEventListener('click', (e) => {
                e.stopPropagation();
                tulisAkun(bacaAkun().filter((x) => x.id !== a.id));
                gambar();
            });

            li.append(tombol, hapus);
            daftarEl.appendChild(li);
        });
    };

    tombolHapusSemua?.addEventListener('click', () => {
        tulisAkun([]);
        gambar();
    });

    gambar();
}

/** Serahkan kredensial ke pengelola sandi peramban agar ia menawarkan menyimpannya. */
function simpanKredensial(form) {
    if (!navigator.credentials || !window.PasswordCredential) return Promise.resolve();

    try {
        const kredensial = new window.PasswordCredential({
            id: form.querySelector('#auth-identifier').value,
            password: form.querySelector('#auth-password').value,
            name: form.querySelector('#auth-identifier').value,
        });
        return navigator.credentials.store(kredensial).catch(() => {});
    } catch {
        return Promise.resolve();
    }
}

/* ─────────────── intip kata sandi ─────────────── */

function pasangIntip() {
    document.querySelectorAll('[data-auth-toggle-password]').forEach((tombol) => {
        tombol.addEventListener('click', () => {
            const input = document.getElementById(tombol.dataset.authTogglePassword);
            if (!input) return;

            const tampil = input.type === 'password';
            input.type = tampil ? 'text' : 'password';
            tombol.setAttribute('aria-label', tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            tombol.setAttribute('aria-pressed', String(tampil));
            tombol.querySelector('[data-eye]').style.display = tampil ? 'none' : '';
            tombol.querySelector('[data-eye-off]').style.display = tampil ? '' : 'none';
            input.focus();
        });
    });
}

/* ─────────────── overlay proses masuk ─────────────── */

function overlayProses() {
    const el = document.getElementById('auth-progress');
    if (!el) return null;

    const langkahEl = el.querySelector('[data-progress-step]');
    const titik = el.querySelectorAll('[data-progress-dots] i');
    let pewaktu = [];

    return {
        jalankan(langkah, selesai) {
            el.classList.add('is-on');
            el.setAttribute('aria-hidden', 'false');
            const perLangkah = kurangiGerak ? 60 : 420;

            langkah.forEach((teks, i) => {
                pewaktu.push(window.setTimeout(() => {
                    if (langkahEl) langkahEl.textContent = teks;
                    titik.forEach((t, n) => t.classList.toggle('is-active', n <= i));
                }, i * perLangkah));
            });

            pewaktu.push(window.setTimeout(selesai, langkah.length * perLangkah));
        },
        hentikan() {
            pewaktu.forEach(window.clearTimeout);
            pewaktu = [];
            el.classList.remove('is-on');
            el.setAttribute('aria-hidden', 'true');
        },
    };
}

/** Langkah proses masuk — disunting di Tampilan Admin › Halaman Login & Loader. */
function langkahProses() {
    try {
        const langkah = JSON.parse(document.getElementById('auth-progress')?.dataset.steps || '[]');
        if (Array.isArray(langkah) && langkah.length) return langkah.map(String);
    } catch {
        /* Isian rusak — pakai bawaan. */
    }
    return ['Kredensial terverifikasi', 'Menyiapkan sesi aman', 'Membuka dashboard'];
}

/* ─────────────── tema terang / gelap ─────────────── */

function pasangTema() {
    document.getElementById('auth-theme-toggle')?.addEventListener('click', () => {
        const akar = document.documentElement;
        const baru = akar.getAttribute('data-dev-theme') === 'light' ? 'dark' : 'light';
        akar.setAttribute('data-dev-theme', baru);
        try {
            // Kunci yang sama dengan panel admin: satu pilihan untuk keduanya.
            localStorage.setItem('dev-theme', baru);
        } catch {
            /* Diblokir — tema tetap berganti untuk kunjungan ini. */
        }
    });
}

/* ─────────────── formulir masuk ─────────────── */

function pasangMasuk() {
    const form = document.getElementById('auth-signin-form');
    if (!form) return;

    const tombol = form.querySelector('[data-auth-submit]');
    const label = tombol?.querySelector('[data-label]');
    const labelDiam = tombol?.dataset.labelIdle || 'Masuk';
    const labelSibuk = tombol?.dataset.labelBusy || 'Memverifikasi…';
    const kotakGalat = document.getElementById('auth-alert');
    const overlay = overlayProses();
    let sibuk = false;

    const setSibuk = (nilai) => {
        sibuk = nilai;
        if (!tombol) return;
        tombol.disabled = nilai;
        tombol.classList.toggle('is-busy', nilai);
        if (label) label.textContent = nilai ? labelSibuk : labelDiam;
    };

    const tampilGalat = (pesan) => {
        if (!kotakGalat) return;
        kotakGalat.querySelector('[data-alert-text]').textContent = pesan;
        kotakGalat.hidden = false;
        form.querySelector('#auth-identifier')?.setAttribute('aria-invalid', 'true');
    };

    const bersihkanGalat = () => {
        if (kotakGalat) kotakGalat.hidden = true;
        form.querySelector('#auth-identifier')?.removeAttribute('aria-invalid');
    };

    const hitungMundur = (detik) => {
        let sisa = parseInt(detik, 10);
        if (Number.isNaN(sisa) || sisa < 1) sisa = 60;
        if (!tombol) return;

        tombol.disabled = true;
        const detak = window.setInterval(() => {
            if (label) label.textContent = `Coba lagi dalam ${sisa} detik`;
            if (sisa <= 0) {
                window.clearInterval(detak);
                tombol.disabled = false;
                if (label) label.textContent = labelDiam;
            }
            sisa -= 1;
        }, 1000);
    };

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (sibuk) return;

        bersihkanGalat();
        setSibuk(true);

        const token = form.querySelector('input[name="_token"]')?.value || '';

        window.fetch(form.action, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token,
            },
            body: new FormData(form),
        })
            .then((res) => res.json().catch(() => ({})).then((data) => ({ res, data })))
            .then(({ res, data }) => {
                if (!res.ok) {
                    setSibuk(false);

                    if (res.status === 429) {
                        // Batas percobaan dari middleware throttle: lama
                        // tunggunya ada di header Retry-After.
                        tampilGalat('Terlalu banyak percobaan masuk. Silakan tunggu sebentar.');
                        hitungMundur(res.headers.get('Retry-After') || 60);
                        return;
                    }

                    if (res.status === 419) {
                        tampilGalat('Sesi halaman kedaluwarsa. Muat ulang halaman lalu coba lagi.');
                        return;
                    }

                    const galat = data.errors || {};
                    tampilGalat(
                        galat.email?.[0] || galat.password?.[0] || data.message || 'Gagal masuk. Periksa kembali data Anda.',
                    );
                    return;
                }

                ingatAkun(form.querySelector('#auth-identifier')?.value);

                const tujuan = data.redirect || form.dataset.redirect || '/';
                const url = tujuan + (tujuan.includes('?') ? '&' : '?') + 'welcome=1';
                const lanjut = () => window.location.assign(url);

                simpanKredensial(form).then(() => {
                    if (overlay) {
                        overlay.jalankan(langkahProses(), lanjut);
                    } else {
                        lanjut();
                    }
                });
            })
            .catch(() => {
                setSibuk(false);
                overlay?.hentikan();
                tampilGalat('Tidak dapat terhubung ke server. Periksa koneksi Anda.');
            });
    });
}

function mulai() {
    pasangPreloader();
    pasangTema();
    pasangIntip();
    pasangAkun();
    pasangMasuk();
    // Dimulai segera; ia menampakkan diri sendiri setelah preloader.
    pasangVanta();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mulai);
} else {
    mulai();
}
