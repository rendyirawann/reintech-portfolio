# Portfolio CMS System — Design Spec

**Date:** 2026-04-27
**Project:** REIN-TECH Portfolio (rendyirawann/reintech-portfolio)
**Stack:** Laravel 12, PostgreSQL (port 5433), Blade, Vite, GSAP Canvas
**Author:** Rendy Irawan

---

## 1. Overview

Build a private, secret-route CMS that allows the portfolio owner to manage all dynamic content without exposing an obvious admin entry point. The system is completely decoupled from the public frontend in layout, CSS, and JS.

**Public frontend:** Unchanged Space Realm design with canvas animations.
**Admin backend:** Separate layout, separate CSS, light/dark theme, clean table-based UI.

---

## 2. Sub-Systems

| # | Sub-System | Scope |
|---|---|---|
| 1 | Auth System | Login, logout, session guard, middleware |
| 2 | Identity CMS | Logo, topbar text, social links, sidebar icon |
| 3 | Section CMS | Per-section title/subtitle/description + visibility toggle |
| 4 | Projects CMS | Parent project CRUD + child image gallery upload |
| 5 | Frontend Display | Dynamic data on public page + project detail modal |

---

## 3. Architecture

### 3.1 Route Prefix
All admin routes are under `/developer-access` prefix — intentionally obscure.

```
/developer-access/auth/login    GET (show form) + POST (authenticate)
/developer-access/auth/logout   POST
/developer-access/dashboard     GET (home stats)
/developer-access/identity      GET + POST
/developer-access/sections      GET + POST (update per section)
/developer-access/projects      GET (index) + POST (store)
/developer-access/projects/{id}/edit    GET + PUT
/developer-access/projects/{id}         DELETE
/developer-access/projects/{id}/images  POST (upload child images)
/developer-access/projects/{id}/images/{imageId}  DELETE
/developer-access/settings      GET + POST (change password)
```

### 3.2 Middleware
Custom middleware: `App\Http\Middleware\DeveloperAuth`
- Checks session key `developer_id`
- Redirects to `/developer-access/auth/login` if not set
- Applied to all routes EXCEPT login GET/POST

### 3.3 Guard Strategy
**No Laravel Auth guard** — custom session-based auth to avoid any overlap with default `users` table guard. Simple: set `session(['developer_id' => $developer->id])` on login, check it in middleware, forget on logout.

---

## 4. Database Schema (PostgreSQL port 5433)

### 4.1 `developer_accounts`
```sql
id              BIGSERIAL PRIMARY KEY
name            VARCHAR(255) NOT NULL
email           VARCHAR(255) UNIQUE NOT NULL
password        VARCHAR(255) NOT NULL        -- bcrypt hashed
remember_token  VARCHAR(100) NULLABLE
last_login_at   TIMESTAMP NULLABLE
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

### 4.2 `portfolio_identity` (single-row, always id=1)
```sql
id                  BIGSERIAL PRIMARY KEY
logo_text           VARCHAR(10)   DEFAULT 'RD'
logo_subtext        VARCHAR(50)   DEFAULT 'reintech.dev'
topbar_status_text  VARCHAR(100)  DEFAULT 'Available for projects'
topbar_role_text    VARCHAR(100)  DEFAULT 'Full Stack Dev'
sidebar_icon_type   VARCHAR(10)   DEFAULT 'text'   -- 'text' | 'image'
sidebar_icon_value  VARCHAR(255)  DEFAULT 'RD'     -- text or image path
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### 4.3 `social_links`
```sql
id          BIGSERIAL PRIMARY KEY
platform    VARCHAR(50) NOT NULL    -- 'github', 'linkedin', 'instagram', 'twitter', etc.
label       VARCHAR(100) NOT NULL
url         VARCHAR(500) NOT NULL
icon_svg    TEXT NULLABLE           -- raw SVG string for custom icons
is_visible  BOOLEAN DEFAULT TRUE
sort_order  INTEGER DEFAULT 0
created_at  TIMESTAMP
updated_at  TIMESTAMP
```

### 4.4 `portfolio_sections`
```sql
id           BIGSERIAL PRIMARY KEY
section_key  VARCHAR(50) UNIQUE NOT NULL  -- 'hero','about','projects','services','contact'
title        VARCHAR(255) NULLABLE
subtitle     VARCHAR(255) NULLABLE
description  TEXT NULLABLE
is_visible   BOOLEAN DEFAULT TRUE
created_at   TIMESTAMP
updated_at   TIMESTAMP
```

### 4.5 `projects` (parent)
```sql
id                BIGSERIAL PRIMARY KEY
title             VARCHAR(255) NOT NULL
slug              VARCHAR(255) UNIQUE NOT NULL
short_description VARCHAR(500) NULLABLE       -- for card on frontend
long_description  TEXT NULLABLE               -- for modal detail
main_image        VARCHAR(500) NULLABLE       -- path to primary image
category          VARCHAR(100) NULLABLE       -- 'Government System', 'SaaS', etc.
year              SMALLINT NULLABLE
live_url          VARCHAR(500) NULLABLE
repo_url          VARCHAR(500) NULLABLE
tech_stack        JSON DEFAULT '[]'           -- ["Laravel","PostgreSQL","Tailwind"]
is_visible        BOOLEAN DEFAULT TRUE
sort_order        INTEGER DEFAULT 0
created_at        TIMESTAMP
updated_at        TIMESTAMP
```

### 4.6 `project_images` (child)
```sql
id          BIGSERIAL PRIMARY KEY
project_id  BIGINT NOT NULL REFERENCES projects(id) ON DELETE CASCADE
image_path  VARCHAR(500) NOT NULL
caption     VARCHAR(255) NULLABLE
sort_order  INTEGER DEFAULT 0
created_at  TIMESTAMP
updated_at  TIMESTAMP
```

---

## 5. File Structure

```
app/
├── Http/
│   ├── Controllers/Developer/
│   │   ├── DeveloperAuthController.php
│   │   ├── DeveloperDashboardController.php
│   │   ├── DeveloperIdentityController.php
│   │   ├── DeveloperSectionController.php
│   │   ├── DeveloperProjectController.php
│   │   └── DeveloperSettingsController.php
│   └── Middleware/
│       └── DeveloperAuth.php
├── Models/
│   ├── DeveloperAccount.php
│   ├── PortfolioIdentity.php
│   ├── SocialLink.php
│   ├── PortfolioSection.php
│   ├── Project.php
│   └── ProjectImage.php
database/
└── migrations/
    ├── xxxx_create_developer_accounts_table.php
    ├── xxxx_create_portfolio_identity_table.php
    ├── xxxx_create_social_links_table.php
    ├── xxxx_create_portfolio_sections_table.php
    ├── xxxx_create_projects_table.php
    └── xxxx_create_project_images_table.php
resources/
├── css/
│   └── developer.css          ← Admin-only CSS, light/dark theme
├── js/
│   └── developer.js           ← Admin-only JS (theme toggle, image preview)
└── views/
    ├── layouts/
    │   └── developer.blade.php   ← Separate admin layout
    └── developer/
        ├── auth/
        │   └── login.blade.php
        ├── dashboard.blade.php
        ├── identity.blade.php
        ├── sections.blade.php
        ├── projects/
        │   ├── index.blade.php
        │   ├── create.blade.php
        │   └── edit.blade.php
        └── settings.blade.php
```

---

## 6. Frontend Public Changes

### 6.1 WelcomeController
`app/Http/Controllers/WelcomeController.php` — loads all data:
```php
public function index() {
    return view('welcome', [
        'identity'  => PortfolioIdentity::first(),
        'socials'   => SocialLink::visible()->ordered()->get(),
        'sections'  => PortfolioSection::all()->keyBy('section_key'),
        'projects'  => Project::visible()->ordered()->with('images')->get(),
    ]);
}
```

### 6.2 Project Detail Modal
- Click project card → JS opens full-screen modal overlay
- Modal renders: main_image hero, title, long_description, tech_stack badges, live_url + repo_url buttons, image gallery grid (project_images children)
- Close: ESC key or click overlay
- Implemented in `app.js` — no new dependencies

---

## 7. Admin Dashboard UI

### 7.1 Layout (`layouts/developer.blade.php`)
- Fixed sidebar left (240px): DEV logo, nav links, logout
- Content area: header with page title + theme toggle button
- Footer: version info
- Light/dark stored in `localStorage('dev-theme')`

### 7.2 Theming
```css
/* Dark (default) */
--dev-bg: #0f172a; --dev-surface: #1e293b; --dev-text: #f8fafc; --dev-accent: #3b82f6;
/* Light */
--dev-bg: #f8fafc; --dev-surface: #ffffff; --dev-text: #0f172a; --dev-accent: #2563eb;
```

### 7.3 Per-Module UI
| Module | UI |
|---|---|
| Identity | Single form, image upload preview for sidebar icon |
| Sections | Cards per section with inline edit form |
| Social Links | Sortable table with visible toggle, add/edit/delete |
| Projects | Table with thumbnail, sort_order, visible toggle |
| Project Edit | Form + multi-image upload with drag-and-drop preview |
| Settings | Password change form |

---

## 8. Seeder
`DeveloperSeeder` creates:
1. One `developer_accounts` record (email: dev@reintech.dev, configurable via env)
2. One `portfolio_identity` row (id=1)
3. Default `social_links` (GitHub, LinkedIn, Instagram, Twitter/X, WhatsApp)
4. Default `portfolio_sections` (hero, about, projects, services, contact)

---

## 9. .env Additions
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5433
DB_DATABASE=reintech_portfolio
DB_USERNAME=postgres
DB_PASSWORD=your_password

DEVELOPER_DEFAULT_EMAIL=dev@reintech.dev
DEVELOPER_DEFAULT_PASSWORD=changeme123
```

---

## 10. Anti-Patterns (DO NOT)
- ❌ Do NOT use Laravel's built-in `Auth::attempt()` or `auth()` guard (conflicts with `users` table)
- ❌ Do NOT share CSS between admin and frontend
- ❌ Do NOT store images outside `storage/app/public/portfolio/` — use `Storage::disk('public')`
- ❌ Do NOT skip `is_visible` checks on frontend queries

---

## 11. Success Criteria
- [ ] Login at `/developer-access/auth/login`, redirect to dashboard
- [ ] All 5 CMS modules functional with CRUD
- [ ] Frontend loads all data from DB (no hardcoded content)
- [ ] Project detail modal opens with gallery
- [ ] Admin light/dark toggle works and persists
- [ ] PostgreSQL on port 5433 connected and migrated
- [ ] Seeder creates default data for first run
