<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>REINTECH | Digital Universe Creator</title>
    <meta name="description" content="Full-Stack Developer & Tech Creator based in Indonesia. Crafting high-performance digital experiences.">
    {{-- Fonts loaded via app.css (Archivo + Space Grotesk) from MASTER.md design system --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- ================================================
     PRELOADER — SPACE REALM FANTASY
================================================ --}}
<div id="preloader">
    <canvas id="preloader-canvas"></canvas>
    <div id="preloader-text">
        <div id="preloader-title">REINTECH</div>
        <div id="preloader-sub">BEYOND THE DIMENSION</div>
    </div>
    <button id="enter-btn">ENTER UNIVERSE</button>
</div>

{{-- ================================================
     SIDEBAR — ICON TUBE (floating, 72px)
================================================ --}}
<aside class="sidebar" id="sidebar">

    <nav class="sidebar-nav">
        <a href="#hero" class="nav-link active" data-label="Home" id="nav-hero">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
            <span class="nav-label">Home</span>
        </a>
        <a href="#about" class="nav-link" data-label="About" id="nav-about">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
            <span class="nav-label">About</span>
        </a>
        <a href="#projects" class="nav-link" data-label="Projects" id="nav-projects">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3l5 9H7l5-9z"/></svg>
            <span class="nav-label">Projects</span>
        </a>
        <a href="#services" class="nav-link" data-label="Services" id="nav-services">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
            <span class="nav-label">Services</span>
        </a>
        <a href="#contact" class="nav-link" data-label="Contact" id="nav-contact">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
            <span class="nav-label">Contact</span>
        </a>
    </nav>

    <div class="sidebar-divider"></div>

    <div class="sidebar-social">
        <a href="https://github.com" target="_blank" class="social-link" data-label="GitHub">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
            <span class="nav-label">GitHub</span>
        </a>
        <a href="https://linkedin.com" target="_blank" class="social-link" data-label="LinkedIn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            <span class="nav-label">LinkedIn</span>
        </a>
        <a href="https://instagram.com" target="_blank" class="social-link" data-label="Instagram">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
            <span class="nav-label">Instagram</span>
        </a>
    </div>
</aside>

{{-- ================================================
     FLOATING TOPBAR
================================================ --}}
<header class="topbar" id="topbar">
    <div class="topbar-left">
        <span class="topbar-status"></span>
        <span class="topbar-available">Available for projects</span>
    </div>
    <div class="topbar-right">
        <span class="topbar-pill">💻 Full-Stack Dev</span>
        <button class="theme-toggle" onclick="window.toggleTheme()" id="theme-toggle" aria-label="Toggle Theme">
            <span id="theme-icon">☀️</span>
        </button>
    </div>
</header>

{{-- ================================================
     MAIN CONTENT
================================================ --}}
<main class="main-content">

    {{-- 🏠 HERO --}}
    <section class="section hero" id="hero" data-section>
        <canvas class="section-canvas" id="hero-canvas"></canvas>
        <div class="hero-glow-1"></div>
        <div class="hero-glow-2"></div>
        <div class="hero-glow-3"></div>
        <div class="section-inner hero-inner">
            <div class="hero-badge">Full-Stack Developer & Digital Creator</div>
            <h1 class="hero-title">
                I Build<br>
                <span class="gradient-text">Digital Things</span><br>
                That Matter
            </h1>
            <p class="hero-desc">
                Crafting high-performance web apps, mobile experiences,<br>
                and stunning digital solutions from Indonesia.
            </p>
            <div class="hero-cta">
                <a href="#projects" class="btn-primary">Explore My Work →</a>
                <a href="#contact" class="btn-ghost">Let's Talk</a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-num counter" data-target="24">0</span><span class="stat-suffix">+</span>
                    <span class="stat-label">Projects</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-num counter" data-target="6">0</span><span class="stat-suffix">+</span>
                    <span class="stat-label">Years Exp</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-num counter" data-target="18">0</span><span class="stat-suffix">+</span>
                    <span class="stat-label">Clients</span>
                </div>
            </div>
        </div>
    </section>

    {{-- 👤 ABOUT — DNA Canvas --}}
    <section class="section" id="about" data-section>
        <canvas class="section-canvas" id="about-canvas"></canvas>
        <div class="section-inner">
            <div class="section-tag reveal">About Me</div>
            <h2 class="section-title reveal">The Mind Behind<br>The Code</h2>
            <div class="about-grid">
                <div class="about-card reveal">
                    <div class="about-icon">🚀</div>
                    <h3>Builder</h3>
                    <p>I turn ideas into fast, beautiful, production-ready applications that real users love.</p>
                </div>
                <div class="about-card reveal">
                    <div class="about-icon">🎨</div>
                    <h3>Designer-Dev</h3>
                    <p>Strong aesthetic sense combined with technical depth — I bridge design and engineering seamlessly.</p>
                </div>
                <div class="about-card reveal">
                    <div class="about-icon">⚡</div>
                    <h3>Optimizer</h3>
                    <p>Performance-obsessed. Every millisecond and pixel matters in what I ship.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 🚀 PROJECTS — Matrix Rain Canvas --}}
    <section class="section" id="projects" data-section>
        <canvas class="section-canvas" id="projects-canvas"></canvas>
        <div class="section-inner">
            <div class="section-tag reveal">My Work</div>
            <h2 class="section-title reveal">Selected Projects &<br>Case Studies</h2>
            <div class="projects-grid">
                <div class="project-card reveal">
                    <div class="project-thumb" style="background: linear-gradient(135deg, #0a0620 0%, #1a0f40 50%, #0f2020 100%);">
                        <div class="project-thumb-icon">🏛️</div>
                        <div class="project-thumb-glow" style="background: radial-gradient(circle, rgba(124,92,252,0.3), transparent 70%)"></div>
                    </div>
                    <div class="project-info">
                        <div class="project-meta">
                            <span class="project-year">2024</span>
                            <span class="project-type">Government System</span>
                        </div>
                        <h3 class="project-title">Perizinan Deli Serdang</h3>
                        <p class="project-desc">Sistem manajemen perizinan terpadu dengan tanda tangan digital (TTE), alur multi-level approval, dan dashboard analytics real-time untuk instansi pemerintah Kabupaten Deli Serdang.</p>
                        <div class="project-tags">
                            <span class="project-tag">Laravel 10</span>
                            <span class="project-tag">PostgreSQL</span>
                            <span class="project-tag">Bootstrap 5</span>
                            <span class="project-tag">Digital TTE</span>
                        </div>
                        <a href="#contact" class="project-link">View Case Study →</a>
                    </div>
                </div>

                <div class="project-card reveal">
                    <div class="project-thumb" style="background: linear-gradient(135deg, #020a14 0%, #0a2040 50%, #041020 100%);">
                        <div class="project-thumb-icon">🚗</div>
                        <div class="project-thumb-glow" style="background: radial-gradient(circle, rgba(56,189,248,0.3), transparent 70%)"></div>
                    </div>
                    <div class="project-info">
                        <div class="project-meta">
                            <span class="project-year">2024</span>
                            <span class="project-type">Fleet Management</span>
                        </div>
                        <h3 class="project-title">Fleet Tracker App</h3>
                        <p class="project-desc">Aplikasi tracking armada kendaraan real-time yang di-deploy di Ubuntu server dengan backend PostgreSQL, dilengkapi dashboard monitoring dan laporan perjalanan otomatis.</p>
                        <div class="project-tags">
                            <span class="project-tag">Laravel 12</span>
                            <span class="project-tag">PostgreSQL</span>
                            <span class="project-tag">Ubuntu VPS</span>
                            <span class="project-tag">Nginx</span>
                        </div>
                        <a href="#contact" class="project-link">View Case Study →</a>
                    </div>
                </div>

                <div class="project-card reveal">
                    <div class="project-thumb" style="background: linear-gradient(135deg, #140a00 0%, #301800 50%, #1a1000 100%);">
                        <div class="project-thumb-icon">🍽️</div>
                        <div class="project-thumb-glow" style="background: radial-gradient(circle, rgba(251,146,60,0.3), transparent 70%)"></div>
                    </div>
                    <div class="project-info">
                        <div class="project-meta">
                            <span class="project-year">2024</span>
                            <span class="project-type">Restaurant POS</span>
                        </div>
                        <h3 class="project-title">Kitchen Management System</h3>
                        <p class="project-desc">Sistem POS dapur terintegrasi dengan perhitungan HPP otomatis, laporan penjualan per menu, dan dashboard financial analytics untuk manajemen restoran.</p>
                        <div class="project-tags">
                            <span class="project-tag">Laravel</span>
                            <span class="project-tag">MySQL</span>
                            <span class="project-tag">Alpine.js</span>
                            <span class="project-tag">HPP Calc</span>
                        </div>
                        <a href="#contact" class="project-link">View Case Study →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 💼 SERVICES — Circuit Canvas --}}
    <section class="section" id="services" data-section>
        <canvas class="section-canvas" id="services-canvas"></canvas>
        <div class="section-inner">
            <div class="section-tag reveal">What I Do</div>
            <h2 class="section-title reveal">Services &<br>Capabilities</h2>
            <div class="services-grid">
                <div class="service-card reveal">
                    <div class="service-icon">💻</div>
                    <h3 class="service-title">Web Development</h3>
                    <p class="service-desc">Full-stack web apps built with Laravel, React, Vue.js — from MVP to production-grade enterprise systems.</p>
                    <div class="service-price">From IDR 5jt</div>
                </div>
                <div class="service-card reveal">
                    <div class="service-icon">📱</div>
                    <h3 class="service-title">Mobile Apps</h3>
                    <p class="service-desc">Cross-platform mobile applications with seamless UX across Android and iOS using modern frameworks.</p>
                    <div class="service-price">From IDR 8jt</div>
                </div>
                <div class="service-card reveal">
                    <div class="service-icon">🏛️</div>
                    <h3 class="service-title">Government Systems</h3>
                    <p class="service-desc">Specialized experience building secure, auditable e-government platforms with TTE, SPBE compliance.</p>
                    <div class="service-price">Custom Quote</div>
                </div>
                <div class="service-card reveal">
                    <div class="service-icon">🚀</div>
                    <h3 class="service-title">Tech Consulting</h3>
                    <p class="service-desc">Architecture review, stack selection, performance optimization, and DevOps setup for your team.</p>
                    <div class="service-price">IDR 500k/hr</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ✉️ CONTACT — Wormhole Canvas --}}
    <section class="section" id="contact" data-section>
        <canvas class="section-canvas" id="contact-canvas"></canvas>
        <div class="section-inner">
            <div class="section-tag reveal">Get In Touch</div>
            <h2 class="section-title reveal">Let's Build<br>Something Together</h2>
            <div class="contact-wrapper reveal">
                <p class="contact-sub">Have a project in mind or want to discuss opportunities?</p>
                <a href="mailto:hello@reintech.dev" class="contact-email">hello@reintech.dev</a>
                <div class="contact-cta">
                    <a href="mailto:hello@reintech.dev" class="btn-primary">Send Email →</a>
                    <a href="https://wa.me/6281234567890" target="_blank" class="btn-ghost">WhatsApp</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <span>© 2026 Rendy — Built with ❤️ & Laravel</span>
        <span class="footer-brand">rdev.tech</span>
    </footer>

</main>

{{-- FLOATING LOGO BOTTOM RIGHT --}}
<div class="floating-logo" id="floating-logo">RD</div>

{{-- MOBILE BOTTOM TAB BAR --}}
<nav class="mobile-tab-bar">
    <a href="#hero" class="tab-item active">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        <span>Home</span>
    </a>
    <a href="#about" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
        <span>About</span>
    </a>
    <a href="#projects" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3l5 9H7l5-9z"/></svg>
        <span>Projects</span>
    </a>
    <a href="#services" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
        <span>Services</span>
    </a>
    <a href="#contact" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
        <span>Contact</span>
    </a>
</nav>

</body>
</html>