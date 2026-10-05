/**
 * Animasi pembuka dashboard — diputar SEKALI setelah login.
 *
 * Diadaptasi dari app-shell.js milik portfolio-pm. Tahapannya:
 *
 *   1. ubin      latar pekat pecah menjadi ubin yang beterbangan
 *   2. pusaran   tiap ubin meninggalkan lingkaran yang berpusar ke tengah
 *   3. kubus     lingkaran-lingkaran itu menempati rusuk kubus yang berputar
 *   4. runtuh    kubus berputar kencang lalu mengerut menjadi satu titik
 *
 * Tahap 2-4 digambar di satu kanvas, sehingga ±90 benda bergerak berada di
 * satu lapisan komposit, bukan 90 elemen DOM yang dianimasikan.
 *
 * Dimuat HANYA saat halaman dibuka dengan ?welcome=1 — lihat layout admin.
 */

const TAU = Math.PI * 2;

const DUR_UBIN = 460;
const JEDA_UBIN = 34;
const DUR_PUSAR = 1250;
const SEBAR_PUSAR = 260;
const TAHAN_KUBUS = 620;
const DUR_RUNTUH = 780;
const DUR_BERSIH = 340;
const PUTARAN_PUSAR = 1.15;

let kurangiGerak = false;
try {
    kurangiGerak = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
} catch {
    kurangiGerak = false;
}

const lunakMasukKeluar = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);
const lunakKeluar = (t) => 1 - Math.pow(1 - t, 3);
const lunakMasuk = (t) => t * t * t;
const jepit = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);

/** Selisih sudut bertanda terpendek dari a ke b. */
function selisihSudut(a, b) {
    let d = (b - a) % TAU;
    if (d > Math.PI) d -= TAU;
    else if (d < -Math.PI) d += TAU;
    return d;
}

const SUDUT_KUBUS = [
    [-1, -1, -1], [1, -1, -1], [1, 1, -1], [-1, 1, -1],
    [-1, -1, 1], [1, -1, 1], [1, 1, 1], [-1, 1, 1],
];

const RUSUK_KUBUS = [
    [0, 1], [1, 2], [2, 3], [3, 0],
    [4, 5], [5, 6], [6, 7], [7, 4],
    [0, 4], [1, 5], [2, 6], [3, 7],
];

/** Titik-titik di kerangka kubus: setiap sudut plus beberapa titik per rusuk. */
function titikKubus(perRusuk) {
    const titik = SUDUT_KUBUS.map(([x, y, z]) => ({ x, y, z, sudut: true }));

    RUSUK_KUBUS.forEach(([ia, ib]) => {
        const a = SUDUT_KUBUS[ia];
        const b = SUDUT_KUBUS[ib];
        for (let s = 1; s <= perRusuk; s++) {
            const t = s / (perRusuk + 1);
            titik.push({
                x: a[0] + (b[0] - a[0]) * t,
                y: a[1] + (b[1] - a[1]) * t,
                z: a[2] + (b[2] - a[2]) * t,
                sudut: false,
            });
        }
    });

    return titik;
}

/** Putar pada sumbu Y, miringkan pada sumbu X, lalu beri perspektif ringan. */
function proyeksi(p, sudut, skala, cx, cy) {
    const x = p.x * Math.cos(sudut) - p.z * Math.sin(sudut);
    const z = p.x * Math.sin(sudut) + p.z * Math.cos(sudut);
    const miring = -0.52;
    const y2 = p.y * Math.cos(miring) - z * Math.sin(miring);
    const z2 = p.y * Math.sin(miring) + z * Math.cos(miring);
    const dalam = 1 / (1 + z2 * 0.17);
    return { x: cx + x * skala * dalam, y: cy + y2 * skala * dalam, dalam };
}

function campurWarna(a, b, t) {
    return a.map((v, i) => Math.round(v + (b[i] - v) * t));
}

function hex(nilai, cadangan) {
    let v = String(nilai || '').trim().replace('#', '');
    if (v.length === 3) v = v.split('').map((c) => c + c).join('');
    if (!/^[0-9a-f]{6}$/i.test(v)) return cadangan;
    return [parseInt(v.slice(0, 2), 16), parseInt(v.slice(2, 4), 16), parseInt(v.slice(4, 6), 16)];
}

/** Tahap 1: pecah overlay menjadi ubin dan catat posisi masing-masing. */
function bangunUbin(overlay, latar) {
    const lebar = Math.max(document.documentElement.clientWidth, 320);
    const tinggi = Math.max(document.documentElement.clientHeight, 320);
    const kolom = Math.max(4, Math.min(12, Math.round(lebar / 150)));
    const baris = Math.max(3, Math.min(10, Math.round(tinggi / 150)));

    const grid = document.createElement('div');
    grid.className = 'app-reveal__grid';
    grid.style.gridTemplateColumns = `repeat(${kolom}, 1fr)`;
    grid.style.gridTemplateRows = `repeat(${baris}, 1fr)`;

    const tengahK = (kolom - 1) / 2;
    const tengahB = (baris - 1) / 2;
    const ubin = [];
    let jedaMaks = 0;

    for (let i = 0; i < kolom * baris; i++) {
        const k = i % kolom;
        const b = Math.floor(i / kolom);

        // Riak dari tengah, diberi acak agar terbaca "pecah", bukan sapuan rapi.
        const jarak = Math.hypot(k - tengahK, b - tengahB);
        const jeda = Math.round(jarak * JEDA_UBIN + Math.random() * JEDA_UBIN * 1.5);
        jedaMaks = Math.max(jedaMaks, jeda);

        const arahX = (k - tengahK) / Math.max(1, tengahK);
        const arahY = (b - tengahB) / Math.max(1, tengahB);

        const el = document.createElement('div');
        el.className = 'app-reveal__tile';
        el.style.background = latar;
        el.style.animationDelay = `${jeda}ms`;
        el.style.setProperty('--app-tile-dur', `${DUR_UBIN}ms`);
        el.style.setProperty('--app-tile-x', `${Math.round(arahX * 90)}px`);
        el.style.setProperty('--app-tile-y', `${Math.round(arahY * 90 - 30)}px`);
        el.style.setProperty('--app-tile-rot', `${(Math.random() * 26 - 13).toFixed(1)}deg`);
        grid.appendChild(el);

        ubin.push({ x: ((k + 0.5) / kolom) * lebar, y: ((b + 0.5) / baris) * tinggi, jeda });
    }

    overlay.appendChild(grid);
    return { grid, ubin, jedaMaks, lebar, tinggi };
}

function buangOverlay(overlay) {
    overlay?.remove();
}

/** Pudarkan latar pekat — itulah yang akhirnya menampakkan dashboard. */
function bersihkan(overlay) {
    overlay.classList.add('is-clearing');
    window.setTimeout(() => buangOverlay(overlay), DUR_BERSIH + 60);
}

/** Tahap 2-4 di satu kanvas di atas overlay yang kini kosong. */
function jalankanPartikel(overlay, panggung, warna) {
    const kanvas = document.createElement('canvas');
    kanvas.className = 'app-reveal__canvas';
    kanvas.setAttribute('aria-hidden', 'true');
    overlay.appendChild(kanvas);

    const ctx = kanvas.getContext('2d');
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    kanvas.width = Math.round(panggung.lebar * dpr);
    kanvas.height = Math.round(panggung.tinggi * dpr);
    ctx.scale(dpr, dpr);

    const cx = panggung.lebar / 2;
    const cy = panggung.tinggi / 2;
    const skalaKubus = Math.min(panggung.lebar, panggung.tinggi) * 0.115;

    const titik = titikKubus(6);
    let pendaratanTerakhir = 0;

    // Setiap lingkaran lahir di tempat sebuah ubin tadinya berada, sehingga
    // puing-puingnya tampak berubah menjadi kawanan.
    const partikel = titik.map((t, i) => {
        const asal = panggung.ubin[i % panggung.ubin.length];
        const dx = asal.x - cx;
        const dy = asal.y - cy;
        const lahir = asal.jeda + DUR_UBIN * 0.3 + Math.random() * SEBAR_PUSAR;
        pendaratanTerakhir = Math.max(pendaratanTerakhir, lahir + DUR_PUSAR);

        return {
            kubus: t,
            lahir,
            r0: Math.hypot(dx, dy) || 1,
            a0: Math.atan2(dy, dx),
            ukuran0: 2 + Math.random() * 2.5,
            ukuran1: t.sudut ? 3.4 : 2.2,
            rona: Math.random(),
        };
    });

    const mulaiKubus = pendaratanTerakhir;
    const akhirKubus = mulaiKubus + TAHAN_KUBUS;
    const akhirRuntuh = akhirKubus + DUR_RUNTUH;

    const w1 = hex(warna.p1, [34, 211, 238]);
    const w2 = hex(warna.p2, [59, 130, 246]);

    const sudutAwal = 0.6;
    let mulai = null;
    let selesai = false;

    const sudutKubus = (t) => {
        if (t <= mulaiKubus) return sudutAwal;
        if (t <= akhirKubus) return sudutAwal + ((t - mulaiKubus) / 1000) * 1.5;
        const tertahan = ((akhirKubus - mulaiKubus) / 1000) * 1.5;
        return sudutAwal + tertahan + lunakMasuk(jepit((t - akhirKubus) / DUR_RUNTUH)) * 7.5;
    };

    const ukuranKubus = (t) =>
        t <= akhirKubus ? skalaKubus : skalaKubus * (1 - lunakMasuk(jepit((t - akhirKubus) / DUR_RUNTUH)));

    function bingkai(kini) {
        if (mulai === null) mulai = kini;
        const t = kini - mulai;
        const sudut = sudutKubus(t);
        const skala = ukuranKubus(t);
        const pudarGlobal = t <= akhirKubus
            ? 1
            : 1 - jepit((t - akhirKubus - DUR_RUNTUH * 0.45) / (DUR_RUNTUH * 0.55));

        ctx.clearRect(0, 0, panggung.lebar, panggung.tinggi);

        const terproyeksi = partikel.map((p) => {
            if (t < p.lahir) return null;

            const sasaran = proyeksi(p.kubus, sudut, skala, cx, cy);
            const lokal = t - p.lahir;
            let pos;
            let jari;
            let alfa = pudarGlobal;

            if (lokal < DUR_PUSAR) {
                const e = lunakMasukKeluar(lokal / DUR_PUSAR);
                const tdx = sasaran.x - cx;
                const tdy = sasaran.y - cy;
                const r1 = Math.hypot(tdx, tdy);
                const a1 = Math.atan2(tdy, tdx);
                // Interpolasi polar dengan putaran tambahan: pusaran, bukan
                // garis lurus ke tengah.
                const rKini = p.r0 + (r1 - p.r0) * e;
                const aKini = p.a0 + (selisihSudut(p.a0, a1) + PUTARAN_PUSAR * TAU) * e;
                pos = { x: cx + Math.cos(aKini) * rKini, y: cy + Math.sin(aKini) * rKini };
                jari = p.ukuran0 + (p.ukuran1 - p.ukuran0) * e;
                alfa *= lunakKeluar(jepit(lokal / 160));
            } else {
                pos = sasaran;
                jari = p.ukuran1 * Math.max(0.35, sasaran.dalam);
            }

            const rgb = campurWarna(w1, w2, p.rona);
            ctx.beginPath();
            ctx.arc(pos.x, pos.y, Math.max(0.4, jari), 0, TAU);
            ctx.fillStyle = `rgba(${rgb[0]},${rgb[1]},${rgb[2]},${alfa.toFixed(3)})`;
            ctx.fill();

            return { pos };
        });

        // Kerangka tipis setelah sudut-sudutnya di tempat, supaya bentuknya
        // terbaca sebagai kubus, bukan awan titik.
        if (t > mulaiKubus - 160) {
            const alfaRusuk = jepit((t - (mulaiKubus - 160)) / 240) * 0.7 * pudarGlobal;
            if (alfaRusuk > 0.01) {
                ctx.lineWidth = 1.2;
                ctx.strokeStyle = `rgba(${w1[0]},${w1[1]},${w1[2]},${alfaRusuk.toFixed(3)})`;
                ctx.beginPath();
                RUSUK_KUBUS.forEach(([a, b]) => {
                    const dari = terproyeksi[a];
                    const ke = terproyeksi[b];
                    if (dari && ke) {
                        ctx.moveTo(dari.pos.x, dari.pos.y);
                        ctx.lineTo(ke.pos.x, ke.pos.y);
                    }
                });
                ctx.stroke();
            }
        }

        if (t < akhirRuntuh) {
            window.requestAnimationFrame(bingkai);
            return;
        }

        if (!selesai) {
            selesai = true;
            bersihkan(overlay);
        }
    }

    window.requestAnimationFrame(bingkai);

    // Jaring pengaman: bila rAF tertahan (tab di latar), tetap dibereskan.
    window.setTimeout(() => {
        if (!selesai) {
            selesai = true;
            bersihkan(overlay);
        }
    }, akhirRuntuh + 1200);
}

function putar(overlay) {
    const gaya = window.getComputedStyle(overlay);
    const latar = gaya.getPropertyValue('--app-reveal-tile').trim() || '#0c1322';
    const warna = {
        p1: gaya.getPropertyValue('--app-reveal-p1').trim() || '#22d3ee',
        p2: gaya.getPropertyValue('--app-reveal-p2').trim() || '#3b82f6',
    };

    // Batalkan cadangan tanpa-JS, karena sekarang skrip yang mengendalikan.
    overlay.style.animation = 'none';

    const panggung = bangunUbin(overlay, latar);
    jalankanPartikel(overlay, panggung, warna);

    // Grid yang sudah kosong akan menahan satu lapisan layar penuh tanpa guna.
    window.setTimeout(() => panggung.grid.remove(), panggung.jedaMaks + DUR_UBIN + 60);
}

function mulaiPembuka() {
    const overlay = document.getElementById('app-reveal');
    if (!overlay) return;

    // Buang ?welcome=1 supaya muat ulang atau tautan yang dibagikan tidak
    // memutarnya lagi.
    try {
        const url = new URL(window.location.href);
        if (url.searchParams.has('welcome')) {
            url.searchParams.delete('welcome');
            window.history.replaceState({}, '', url.pathname + url.search + url.hash);
        }
    } catch {
        /* URL API tidak tersedia — animasinya tetap jalan. */
    }

    if (kurangiGerak) {
        overlay.style.transition = 'opacity 200ms linear';
        overlay.style.opacity = '0';
        window.setTimeout(() => buangOverlay(overlay), 240);
        return;
    }

    // Batas mutlak, berbasis setTimeout — BUKAN rAF. Pemutaran dimulai di
    // dalam requestAnimationFrame, dan peramban membekukan rAF pada tab di
    // latar (misalnya bila login lalu langsung pindah tab). Tanpa batas ini,
    // overlay pekat menutupi dashboard tanpa jalan keluar selama rAF beku.
    // Urutan lengkapnya sekitar 3,5 detik, jadi 7 detik tidak pernah
    // memotong animasi yang sedang berjalan normal.
    window.setTimeout(() => buangOverlay(overlay), 7000);

    // Biarkan peramban melukis satu bingkai overlay pekat lebih dulu.
    window.requestAnimationFrame(() => window.requestAnimationFrame(() => putar(overlay)));
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mulaiPembuka);
} else {
    mulaiPembuka();
}
