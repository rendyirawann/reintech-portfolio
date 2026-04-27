# REIN-TECH Portfolio — Agentic Development Rules

## 🎨 Design System
> **MASTER SOURCE OF TRUTH:** `design-system/reintech/MASTER.md`
>
> When building any UI component or page, **always read MASTER.md first**.
> Check `design-system/reintech/pages/[page-name].md` for page-specific overrides.

### Active Design System (UI UX Pro Max — Generated 2026-04-27)
| Token | Value | Usage |
|---|---|---|
| `--color-background` | `#0B0B10` | OLED deep black background |
| `--color-foreground` | `#F8FAFC` | Star white text |
| `--color-accent-cta` | `#3B82F6` | Launch blue — primary CTA buttons |
| `--accent` | `#7c5cfc` | Space purple — canvas animations |
| `--accent-2` | `#fc5ca0` | Space pink — DNA strand 2 |
| `--accent-cyan` | `#5cf4fc` | Cyan — DNA right helix |
| `--text-muted` | `#94A3B8` | Secondary text |
| `--color-muted` | `#232328` | Surface/card backgrounds |
| `--color-border` | `#1E293B` | Borders |

### Typography (MASTER.md)
- **Headings:** `Archivo` (300–700)
- **Body:** `Space Grotesk` (300–700)
- **Import:** Loaded via `app.css` `@import` — do NOT add `<link>` tags in Blade

### Spacing Scale
`--space-xs: 4px` · `--space-sm: 8px` · `--space-md: 16px` · `--space-lg: 24px` · `--space-xl: 32px` · `--space-2xl: 48px` · `--space-3xl: 64px`

### Shadow Depths
`--shadow-sm` · `--shadow-md` · `--shadow-lg` · `--shadow-xl` · `--shadow-glow`

---

## 🛠 Tech Stack
- **Framework**: Laravel 12 (Blade, Tailwind CSS v4)
- **Animation**: GSAP v3.15.0, Canvas API (6-canvas system)
- **Build Tool**: Vite 6
- **Font**: Archivo (headings) + Space Grotesk (body)

## 🚀 Agent Skills & Methodology
- **Design Intelligence**: `ui-ux-pro-max-skill` — 67 styles, 161 reasoning rules
- **Workflow**: `superpowers` — brainstorm → plan → TDD → subagent execution
- **Optimization**: `ECC` — Laravel security, PHP patterns, TypeScript rules
- **Laravel Skills**: `laravel-patterns`, `laravel-security`, `laravel-tdd`, `laravel-verification`

## 📜 Development Rules (NON-NEGOTIABLE)

### Canvas Animation System (6 layers — DO NOT BREAK)
| Section | Canvas ID | Animation |
|---|---|---|
| Preloader | `preloader-canvas` | Nebula + Warp Stars |
| Hero | `hero-canvas` | Orbs + DNA Helix x2 + Network 130px + Dust |
| About | `about-canvas` | DNA Double Helix |
| Projects | `projects-canvas` | Matrix Rain (Japanese chars) |
| Services | `services-canvas` | Circuit Pulse |
| Contact | `contact-canvas` | Wormhole / Concentric Rings |

### Layout Rules
- **Bleeding edge**: `.main-content` has NO padding — sections are 100% width
- **Inner container**: `.section-inner` max-width `1200px`, padding `0 60px`
- **Sidebar**: 72px floating pill, left `16px`, `border-radius: 36px`
- **Mobile**: Bottom Tab Bar (no sidebar), `padding-bottom: 90px`

### Anti-Patterns (FORBIDDEN)
- ❌ `npm install laravel/breeze` or any scaffolding that overwrites CSS/JS
- ❌ Adding `<link>` font tags in Blade (fonts loaded via CSS)
- ❌ Emojis as icons — use SVG (Heroicons/Lucide)
- ❌ Generic colors or designs — always reference MASTER.md first
- ❌ Removing canvas elements from welcome.blade.php
- ❌ Missing `cursor-pointer` on interactive elements
- ❌ Transitions under 150ms or over 500ms

### Git Workflow
- Semantic commits: `feat:`, `fix:`, `refactor:`, `style:`, `docs:`
- Always run `npm run build` before committing

## 📂 Project Structure
```
.claude/
├── rules/common/       ← ECC universal rules (always active)
├── rules/php/          ← PHP/Laravel security & patterns
├── rules/typescript/   ← JS/TS rules
└── skills/
    ├── superpowers/    ← 14 workflow skills
    ├── ui-ux-pro-max/  ← 67 styles, 161 rules, CSV database
    ├── laravel-patterns/
    ├── laravel-security/
    ├── laravel-tdd/
    └── laravel-verification/

design-system/
└── reintech/
    ├── MASTER.md       ← Global Source of Truth
    └── pages/          ← Page-specific overrides

resources/
├── css/app.css         ← Design tokens + layout (bleeding edge)
├── js/app.js           ← 6-canvas animation system
└── views/
    └── welcome.blade.php ← Master blade with all canvas IDs
```
