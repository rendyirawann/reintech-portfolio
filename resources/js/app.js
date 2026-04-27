import './bootstrap';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

/* ============================================
   THEME TOGGLE
============================================ */
const initTheme = () => {
    const saved = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', saved);
    updateIcon(saved);
};
const toggleTheme = () => {
    const cur = document.documentElement.getAttribute('data-theme');
    const next = cur === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateIcon(next);
};
const updateIcon = (t) => {
    const el = document.getElementById('theme-icon');
    if (el) el.textContent = t === 'dark' ? '☀️' : '🌙';
};
window.toggleTheme = toggleTheme;

/* ============================================
   SIDEBAR ACTIVE STATE
============================================ */
const initSidebar = () => {
    const sections = document.querySelectorAll('[data-section]');
    const navLinks = document.querySelectorAll('.nav-link');
    const tabItems = document.querySelectorAll('.tab-item');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                const id = e.target.id;
                navLinks.forEach(l => l.classList.toggle('active', l.getAttribute('href') === `#${id}`));
                tabItems.forEach(l => l.classList.toggle('active', l.getAttribute('href') === `#${id}`));
            }
        });
    }, { threshold: 0.4 });
    sections.forEach(s => observer.observe(s));
};

/* ============================================
   PRELOADER — SPACE REALM FANTASY
============================================ */
const initPreloader = () => {
    const overlay  = document.getElementById('preloader');
    const canvas   = document.getElementById('preloader-canvas');
    const enterBtn = document.getElementById('enter-btn');
    if (!overlay || !canvas) return;

    const ctx = canvas.getContext('2d');
    let W = canvas.width  = window.innerWidth;
    let H = canvas.height = window.innerHeight;
    let animId, entered = false;

    window.addEventListener('resize', () => {
        W = canvas.width  = window.innerWidth;
        H = canvas.height = window.innerHeight;
    });

    // Warp stars
    const stars = Array.from({ length: 320 }, () => ({
        x: Math.random() * W - W / 2, y: Math.random() * H - H / 2,
        z: Math.random() * W, pz: 0,
    }));

    // Nebula blobs
    const nebula = Array.from({ length: 80 }, () => ({
        x: Math.random() * W, y: Math.random() * H,
        r: Math.random() * 80 + 20, a: Math.random() * 0.04 + 0.01,
        dx: (Math.random() - .5) * .35, dy: (Math.random() - .5) * .35,
        hue: Math.random() * 80 + 220,
    }));

    let speed = 3, tick = 0;

    const drawPreloader = () => {
        tick++;
        ctx.fillStyle = 'rgba(2,2,8,0.22)';
        ctx.fillRect(0, 0, W, H);

        // Nebula
        nebula.forEach(n => {
            n.x += n.dx; n.y += n.dy;
            if (n.x < -n.r) n.x = W + n.r; if (n.x > W + n.r) n.x = -n.r;
            if (n.y < -n.r) n.y = H + n.r; if (n.y > H + n.r) n.y = -n.r;
            const g = ctx.createRadialGradient(n.x, n.y, 0, n.x, n.y, n.r);
            g.addColorStop(0, `hsla(${n.hue},80%,60%,${n.a})`);
            g.addColorStop(1, 'transparent');
            ctx.beginPath(); ctx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
            ctx.fillStyle = g; ctx.fill();
        });

        // Warp stars
        ctx.save(); ctx.translate(W / 2, H / 2);
        stars.forEach(s => {
            s.pz = s.z; s.z -= speed;
            if (s.z <= 0) { s.x = Math.random() * W - W/2; s.y = Math.random() * H - H/2; s.z = W; s.pz = s.z; }
            const sx = (s.x / s.z) * W; const sy = (s.y / s.z) * H;
            const px = (s.x / s.pz) * W; const py = (s.y / s.pz) * H;
            const b = 1 - s.z / W; const sz = Math.max(0.5, b * 3);
            ctx.beginPath(); ctx.moveTo(px, py); ctx.lineTo(sx, sy);
            ctx.strokeStyle = `rgba(${Math.round(180+75*b)},${Math.round(160+95*b)},255,${b})`;
            ctx.lineWidth = sz; ctx.stroke();
        });
        ctx.restore();

        // Center vortex
        const vr = 60 + Math.sin(tick * 0.02) * 10;
        const vg = ctx.createRadialGradient(W/2, H/2, 0, W/2, H/2, vr * 2);
        vg.addColorStop(0, 'rgba(140,100,255,0.25)');
        vg.addColorStop(0.5, 'rgba(80,40,200,0.08)');
        vg.addColorStop(1, 'transparent');
        ctx.beginPath(); ctx.arc(W/2, H/2, vr * 2, 0, Math.PI * 2);
        ctx.fillStyle = vg; ctx.fill();

        animId = requestAnimationFrame(drawPreloader);
        speed = Math.min(speed + 0.008, 22);
    };

    drawPreloader();
    gsap.to('#enter-btn', { scale: 1.06, duration: 1.2, repeat: -1, yoyo: true, ease: 'sine.inOut' });

    const enter = () => {
        if (entered) return; entered = true;
        cancelAnimationFrame(animId);
        gsap.timeline()
            .to('#preloader-canvas', { scale: 8, opacity: 0, duration: 1.2, ease: 'power3.in' })
            .to('#preloader-text',   { y: -40, opacity: 0, duration: 0.5, ease: 'power2.in' }, 0)
            .to('#enter-btn',        { y: 30, opacity: 0, duration: 0.4, ease: 'power2.in' }, 0)
            .to(overlay,             { opacity: 0, duration: 0.5, ease: 'power2.in' }, 0.8)
            .call(() => {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
                initScrollAnimations();
            });
    };
    enterBtn.addEventListener('click', enter);
    overlay.addEventListener('wheel',      enter, { once: true });
    overlay.addEventListener('touchstart', enter, { once: true });
    document.body.style.overflow = 'hidden';
};

/* ============================================
   HERO CANVAS — Orbs + DNA Helix + Network + Dust
   (12:49 AM Full Rich Triple Layer)
============================================ */
const initHeroCanvas = () => {
    const canvas = document.getElementById('hero-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    const resize = () => {
        canvas.width  = canvas.offsetWidth  || window.innerWidth;
        canvas.height = canvas.offsetHeight || window.innerHeight;
    };
    resize();
    window.addEventListener('resize', resize);

    const isDark = () => document.documentElement.getAttribute('data-theme') !== 'light';

    // LAYER 1: Big glowing orbs (10 pcs)
    const orbs = Array.from({ length: 10 }, () => ({
        x: Math.random() * canvas.width,  y: Math.random() * canvas.height,
        r: Math.random() * 60 + 30,
        dx: (Math.random() - .5) * .25,   dy: (Math.random() - .5) * .25,
        hue: Math.random() > .5 ? 265 : 320,
        alpha: Math.random() * .12 + .06,
        pulse: Math.random() * Math.PI * 2,
    }));

    // LAYER 2: Network particles (80 pcs — strong connections 130px)
    const network = Array.from({ length: 80 }, () => ({
        x: Math.random() * canvas.width,  y: Math.random() * canvas.height,
        r: Math.random() * 1.8 + .5,
        dx: (Math.random() - .5) * .6,    dy: (Math.random() - .5) * .6,
        alpha: Math.random() * .5 + .2,
    }));

    // LAYER 3: Ambient dust (120 tiny)
    const dust = Array.from({ length: 120 }, () => ({
        x: Math.random() * canvas.width,  y: Math.random() * canvas.height,
        r: Math.random() * 1.2 + .2,
        dx: (Math.random() - .5) * .2,    dy: (Math.random() - .5) * .2,
        alpha: Math.random() * .35 + .05,
    }));

    let tick = 0;

    const draw = () => {
        tick++;
        const W = canvas.width; const H = canvas.height;
        ctx.clearRect(0, 0, W, H);
        const col = isDark() ? '124,92,252' : '100,70,220';

        // Draw orbs
        orbs.forEach(o => {
            o.pulse += .008;
            o.x += o.dx; o.y += o.dy;
            if (o.x < -o.r) o.x = W + o.r; if (o.x > W + o.r) o.x = -o.r;
            if (o.y < -o.r) o.y = H + o.r; if (o.y > H + o.r) o.y = -o.r;
            const pr = o.r + Math.sin(o.pulse) * 8;
            const g = ctx.createRadialGradient(o.x, o.y, 0, o.x, o.y, pr * 2.5);
            g.addColorStop(0, `hsla(${o.hue},80%,65%,${o.alpha + Math.sin(o.pulse) * .03})`);
            g.addColorStop(.4, `hsla(${o.hue},80%,65%,${o.alpha * .4})`);
            g.addColorStop(1, 'transparent');
            ctx.beginPath(); ctx.arc(o.x, o.y, pr * 2.5, 0, Math.PI * 2);
            ctx.fillStyle = g; ctx.fill();
        });

        // Draw DNA helix — LEFT side
        for (let i = 0; i < 60; i++) {
            const t = (i / 60) * Math.PI * 6 + tick * 0.015;
            const amp = 60 + Math.sin(tick * 0.008) * 15;
            const cx = W * 0.12;
            const x1 = cx + Math.sin(t) * amp; const y1 = (i / 60) * H;
            const x2 = cx - Math.sin(t) * amp;
            const sz = Math.abs(3 + Math.cos(t) * 2);
            ctx.beginPath(); ctx.arc(x1, y1, sz, 0, Math.PI*2);
            ctx.fillStyle = `rgba(124,92,252,${0.12 + Math.cos(t)*0.06})`; ctx.fill();
            ctx.beginPath(); ctx.arc(x2, y1, sz, 0, Math.PI*2);
            ctx.fillStyle = `rgba(252,92,160,${0.10 + Math.sin(t)*0.05})`; ctx.fill();
            if (i % 5 === 0) {
                ctx.beginPath(); ctx.moveTo(x1, y1); ctx.lineTo(x2, y1);
                ctx.strokeStyle = `rgba(92,244,252,0.06)`; ctx.lineWidth = 1; ctx.stroke();
            }
        }

        // Draw DNA helix — RIGHT side (mirrored phase)
        for (let i = 0; i < 60; i++) {
            const t = (i / 60) * Math.PI * 6 + tick * 0.015 + Math.PI;
            const amp = 60 + Math.sin(tick * 0.008) * 15;
            const cx = W * 0.88;
            const x1 = cx + Math.sin(t) * amp; const y1 = (i / 60) * H;
            const x2 = cx - Math.sin(t) * amp;
            const sz = Math.abs(3 + Math.cos(t) * 2);
            ctx.beginPath(); ctx.arc(x1, y1, sz, 0, Math.PI*2);
            ctx.fillStyle = `rgba(92,244,252,${0.10 + Math.cos(t)*0.05})`; ctx.fill();
            ctx.beginPath(); ctx.arc(x2, y1, sz, 0, Math.PI*2);
            ctx.fillStyle = `rgba(124,92,252,${0.08 + Math.sin(t)*0.04})`; ctx.fill();
            if (i % 5 === 0) {
                ctx.beginPath(); ctx.moveTo(x1, y1); ctx.lineTo(x2, y1);
                ctx.strokeStyle = `rgba(252,92,160,0.05)`; ctx.lineWidth = 1; ctx.stroke();
            }
        }

        // Draw network particles
        network.forEach(p => {
            p.x += p.dx; p.y += p.dy;
            if (p.x < 0 || p.x > W) p.dx *= -1;
            if (p.y < 0 || p.y > H) p.dy *= -1;
            ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(${col},${p.alpha})`; ctx.fill();
        });
        // Network connections (130px range)
        for (let i = 0; i < network.length; i++) {
            for (let j = i + 1; j < network.length; j++) {
                const d = Math.hypot(network[i].x - network[j].x, network[i].y - network[j].y);
                if (d < 130) {
                    ctx.beginPath(); ctx.moveTo(network[i].x, network[i].y); ctx.lineTo(network[j].x, network[j].y);
                    ctx.strokeStyle = `rgba(${col},${(1 - d / 130) * .15})`;
                    ctx.lineWidth = .8; ctx.stroke();
                }
            }
        }

        // Draw ambient dust
        dust.forEach(p => {
            p.x += p.dx; p.y += p.dy;
            if (p.x < 0 || p.x > W) p.dx *= -1;
            if (p.y < 0 || p.y > H) p.dy *= -1;
            ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255,255,255,${p.alpha})`; ctx.fill();
        });

        requestAnimationFrame(draw);
    };
    draw();
};

/* ============================================
   ABOUT CANVAS — DNA Double Helix
============================================ */
const initAboutCanvas = () => {
    const canvas = document.getElementById('about-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let W = canvas.width  = canvas.offsetWidth;
    let H = canvas.height = canvas.offsetHeight;
    let tick = 0;
    const draw = () => {
        tick++; ctx.clearRect(0, 0, W, H);
        for (let i = 0; i < 60; i++) {
            const t = (i / 60) * Math.PI * 6 + tick * 0.015;
            const x = W/2 + Math.sin(t) * 80; const y = (i / 60) * H;
            const x2 = W/2 - Math.sin(t) * 80;
            const sz = Math.abs(4 + Math.cos(t) * 2);
            ctx.beginPath(); ctx.arc(x, y, sz, 0, Math.PI*2);
            ctx.fillStyle = `rgba(124,92,252,${0.15 + Math.cos(t)*.1})`; ctx.fill();
            ctx.beginPath(); ctx.arc(x2, y, sz, 0, Math.PI*2);
            ctx.fillStyle = `rgba(252,92,160,${0.12 + Math.sin(t)*.08})`; ctx.fill();
            if (i % 5 === 0) {
                ctx.beginPath(); ctx.moveTo(x, y); ctx.lineTo(x2, y);
                ctx.strokeStyle = `rgba(92,244,252,0.08)`; ctx.lineWidth = 1; ctx.stroke();
            }
        }
        requestAnimationFrame(draw);
    };
    draw();
    window.addEventListener('resize', () => { W = canvas.width = canvas.offsetWidth; H = canvas.height = canvas.offsetHeight; });
};

/* ============================================
   PROJECTS CANVAS — Matrix Rain
============================================ */
const initProjectsCanvas = () => {
    const canvas = document.getElementById('projects-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let W = canvas.width  = canvas.offsetWidth;
    let H = canvas.height = canvas.offsetHeight;
    const fs = 14;
    const cols = Math.floor(W / fs);
    const drops = Array(cols).fill(1);
    const chars = 'アイウエオカキクケコサシスセソ0123456789ABCDEF</>{}[]';
    const draw = () => {
        ctx.fillStyle = 'rgba(5,5,8,0.05)'; ctx.fillRect(0, 0, W, H);
        ctx.font = `${fs}px monospace`;
        drops.forEach((y, i) => {
            const c = chars[Math.floor(Math.random() * chars.length)];
            const alpha = Math.random() * 0.25 + 0.04;
            ctx.fillStyle = `rgba(92,252,160,${alpha})`;
            ctx.fillText(c, i * fs, y * fs);
            if (y * fs > H && Math.random() > 0.975) drops[i] = 0;
            drops[i]++;
        });
        requestAnimationFrame(draw);
    };
    draw();
    window.addEventListener('resize', () => { W = canvas.width = canvas.offsetWidth; H = canvas.height = canvas.offsetHeight; });
};

/* ============================================
   SERVICES CANVAS — Circuit Pulse
============================================ */
const initServicesCanvas = () => {
    const canvas = document.getElementById('services-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let W = canvas.width  = canvas.offsetWidth;
    let H = canvas.height = canvas.offsetHeight;
    const nodes = Array.from({ length: 22 }, () => ({
        x: Math.random() * W, y: Math.random() * H,
        pulse: Math.random() * Math.PI * 2,
    }));
    const edges = [];
    nodes.forEach((a, i) => nodes.slice(i+1).forEach(b => {
        if (Math.hypot(a.x-b.x, a.y-b.y) < 200) edges.push([a, b]);
    }));
    const draw = () => {
        ctx.clearRect(0, 0, W, H);
        edges.forEach(([a, b]) => {
            ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y);
            ctx.strokeStyle = `rgba(124,92,252,${0.04 + Math.sin((a.pulse+b.pulse)/2)*.04})`;
            ctx.lineWidth = 1; ctx.stroke();
        });
        nodes.forEach(n => {
            n.pulse += 0.015;
            const r = 4 + Math.sin(n.pulse) * 3;
            const g = ctx.createRadialGradient(n.x, n.y, 0, n.x, n.y, r*3);
            g.addColorStop(0, `rgba(124,92,252,${0.3+Math.sin(n.pulse)*.2})`);
            g.addColorStop(1, 'transparent');
            ctx.beginPath(); ctx.arc(n.x, n.y, r*3, 0, Math.PI*2);
            ctx.fillStyle = g; ctx.fill();
        });
        requestAnimationFrame(draw);
    };
    draw();
    window.addEventListener('resize', () => { W = canvas.width = canvas.offsetWidth; H = canvas.height = canvas.offsetHeight; });
};

/* ============================================
   CONTACT CANVAS — Wormhole / Concentric Rings
============================================ */
const initContactCanvas = () => {
    const canvas = document.getElementById('contact-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let W = canvas.width  = canvas.offsetWidth;
    let H = canvas.height = canvas.offsetHeight;
    let tick = 0;
    const draw = () => {
        tick++; ctx.clearRect(0, 0, W, H);
        for (let i = 8; i > 0; i--) {
            const r = (i * 45) + Math.sin(tick * 0.02 + i) * 18;
            const g = ctx.createRadialGradient(W/2, H/2, r*.1, W/2, H/2, r);
            g.addColorStop(0, `rgba(124,92,252,${0.06-i*0.005})`);
            g.addColorStop(.5, `rgba(252,92,160,${0.03-i*0.002})`);
            g.addColorStop(1, 'transparent');
            ctx.beginPath(); ctx.arc(W/2, H/2, r, 0, Math.PI*2);
            ctx.strokeStyle = `rgba(124,92,252,${0.1-i*0.008})`;
            ctx.lineWidth = 1.5; ctx.stroke();
            ctx.fillStyle = g; ctx.fill();
        }
        requestAnimationFrame(draw);
    };
    draw();
    window.addEventListener('resize', () => { W = canvas.width = canvas.offsetWidth; H = canvas.height = canvas.offsetHeight; });
};

/* ============================================
   GSAP SCROLL ANIMATIONS
============================================ */
const initScrollAnimations = () => {
    // Force visible items already in view
    document.querySelectorAll('.reveal').forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight) {
            gsap.set(el, { opacity: 1, y: 0 });
        }
    });

    ScrollTrigger.batch('.reveal', {
        onEnter: (els) => gsap.to(els, {
            opacity: 1, y: 0, duration: 0.9, stagger: 0.12, ease: 'power3.out',
        }),
        start: 'top 92%',
        once: true,
    });

    // Hero entrance
    gsap.timeline({ defaults: { ease: 'power3.out' } })
        .from('.hero-badge',  { y: 20, opacity: 0, duration: 0.6 })
        .from('.hero-title',  { y: 40, opacity: 0, duration: 0.8 }, '-=0.3')
        .from('.hero-desc',   { y: 30, opacity: 0, duration: 0.7 }, '-=0.5')
        .from('.hero-cta',    { y: 20, opacity: 0, duration: 0.6 }, '-=0.4')
        .from('.hero-stats',  { y: 20, opacity: 0, duration: 0.6 }, '-=0.3');

    // Counter animation
    document.querySelectorAll('.counter').forEach(el => {
        const target = parseInt(el.dataset.target);
        ScrollTrigger.create({
            trigger: el, start: 'top 90%', once: true,
            onEnter: () => gsap.to({ val: 0 }, {
                val: target, duration: 1.5, ease: 'power2.out',
                onUpdate: function() { el.textContent = Math.round(this.targets()[0].val); },
            }),
        });
    });

    // Topbar scroll opacity
    ScrollTrigger.create({
        start: 'top -60px',
        onUpdate: (self) => {
            const t = document.querySelector('.topbar');
            if (!t) return;
            t.style.background = self.progress > 0
                ? 'rgba(5,5,8,0.95)'
                : '';
        }
    });

    ScrollTrigger.refresh();
};

/* ============================================
   CURSOR GLOW
============================================ */
const initCursorGlow = () => {
    const glow = document.createElement('div');
    glow.style.cssText = `
        position:fixed;width:350px;height:350px;border-radius:50%;
        background:radial-gradient(circle,rgba(124,92,252,0.06),transparent 70%);
        pointer-events:none;z-index:0;transform:translate(-50%,-50%);
        transition:opacity .3s ease;opacity:0;
    `;
    document.body.appendChild(glow);
    document.addEventListener('mousemove', e => {
        glow.style.left = e.clientX + 'px';
        glow.style.top  = e.clientY + 'px';
        glow.style.opacity = '1';
    });
    document.addEventListener('mouseleave', () => glow.style.opacity = '0');
};

/* ============================================
   INIT
============================================ */
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSidebar();
    initPreloader();
    initHeroCanvas();
    initAboutCanvas();
    initProjectsCanvas();
    initServicesCanvas();
    initContactCanvas();
    initCursorGlow();
});