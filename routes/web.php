<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Developer\DeveloperAuthController;
use App\Http\Controllers\Developer\DeveloperContentController;
use App\Http\Controllers\Developer\DeveloperDashboardController;
use App\Http\Controllers\Developer\DeveloperExportController;
use App\Http\Controllers\Developer\DeveloperIdentityController;
use App\Http\Controllers\Developer\DeveloperSectionController;
use App\Http\Controllers\Developer\DeveloperProjectController;
use App\Http\Controllers\Developer\DeveloperSettingsController;

// ─── PUBLIC ────────────────────────────────────────────────
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Dibangkitkan dari basis data supaya tidak pernah usang.
Route::get('/verifikasi/{kode?}', [\App\Http\Controllers\VerifikasiController::class, 'tampil'])->where('kode', '[A-Za-z0-9]{1,12}')->middleware('throttle:60,1')->name('verifikasi');
Route::post('/verifikasi', [\App\Http\Controllers\VerifikasiController::class, 'periksa'])->middleware('throttle:10,1')->name('verifikasi.periksa');
Route::get('/proyek', \App\Http\Controllers\ProjectListController::class)->middleware('throttle:60,1')->name('projects.list');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/site.webmanifest', [SeoController::class, 'manifest'])->name('manifest');

// ─── DEVELOPER AUTH (no middleware) ────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [DeveloperAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [DeveloperAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
    Route::post('/logout',[DeveloperAuthController::class, 'logout'])->name('logout');
});

// ─── DEVELOPER PANEL (protected) ───────────────────────────
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('/dashboard', [DeveloperDashboardController::class, 'index'])->name('dashboard');

    // Identity (logo, topbar, sidebar)
    Route::get('/identity',  [DeveloperIdentityController::class, 'index'])->name('identity');
    Route::post('/identity', [DeveloperIdentityController::class, 'update'])->name('identity.update');

    // Social Links
    Route::get('/identity/socials',         [DeveloperIdentityController::class, 'socials'])->name('identity.socials');
    Route::post('/identity/socials',        [DeveloperIdentityController::class, 'storeSocial'])->name('identity.socials.store');
    Route::put('/identity/socials/{id}',    [DeveloperIdentityController::class, 'updateSocial'])->name('identity.socials.update');
    Route::delete('/identity/socials/{id}', [DeveloperIdentityController::class, 'destroySocial'])->name('identity.socials.destroy');
    Route::post('/identity/socials/{id}/toggle', [DeveloperIdentityController::class, 'toggleSocial'])->name('identity.socials.toggle');

    // Sections
    Route::get('/sections',      [DeveloperSectionController::class, 'index'])->name('sections');
    Route::put('/sections/{id}', [DeveloperSectionController::class, 'update'])->name('sections.update');
    Route::post('/sections/{id}/toggle', [DeveloperSectionController::class, 'toggle'])->name('sections.toggle');

    // Hero Stats (child of hero section)
    Route::post('/sections/hero-stats',           [DeveloperSectionController::class, 'storeHeroStat'])->name('sections.hero-stats.store');
    Route::put('/sections/hero-stats/{id}',       [DeveloperSectionController::class, 'updateHeroStat'])->name('sections.hero-stats.update');
    Route::delete('/sections/hero-stats/{id}',    [DeveloperSectionController::class, 'destroyHeroStat'])->name('sections.hero-stats.destroy');

    // About Cards (child of about section)
    Route::post('/sections/about-cards',          [DeveloperSectionController::class, 'storeAboutCard'])->name('sections.about-cards.store');
    Route::put('/sections/about-cards/{id}',      [DeveloperSectionController::class, 'updateAboutCard'])->name('sections.about-cards.update');
    Route::delete('/sections/about-cards/{id}',   [DeveloperSectionController::class, 'destroyAboutCard'])->name('sections.about-cards.destroy');

    // Service Items (child of services section)
    Route::post('/sections/service-items',        [DeveloperSectionController::class, 'storeServiceItem'])->name('sections.service-items.store');
    Route::put('/sections/service-items/{id}',    [DeveloperSectionController::class, 'updateServiceItem'])->name('sections.service-items.update');
    Route::delete('/sections/service-items/{id}', [DeveloperSectionController::class, 'destroyServiceItem'])->name('sections.service-items.destroy');

    // Projects
    Route::get('/projects',            [DeveloperProjectController::class, 'index'])->name('projects');
    Route::get('/projects/create',     [DeveloperProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects',           [DeveloperProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{id}/edit',  [DeveloperProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{id}',       [DeveloperProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{id}',    [DeveloperProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/projects/{id}/toggle', [DeveloperProjectController::class, 'toggle'])->name('projects.toggle');

    // Anak-anak proyek: gambar, berkas, tautan
    Route::post('/projects/{id}/images',          [DeveloperProjectController::class, 'uploadImages'])->name('projects.images.upload');
    Route::delete('/projects/images/{imageId}',   [DeveloperProjectController::class, 'destroyImage'])->name('projects.images.destroy');
    Route::post('/projects/{id}/files',           [DeveloperProjectController::class, 'uploadFiles'])->name('projects.files.upload');
    Route::delete('/projects/files/{fileId}',     [DeveloperProjectController::class, 'destroyFile'])->name('projects.files.destroy');
    Route::post('/projects/{id}/links',           [DeveloperProjectController::class, 'storeLink'])->name('projects.links.store');
    Route::delete('/projects/links/{linkId}',     [DeveloperProjectController::class, 'destroyLink'])->name('projects.links.destroy');

    // Ekspor portofolio. Dibatasi 6 per menit: setiap permintaan menyalakan
    // satu proses Chromium, dan tanpa batas tombol yang diklik berulang bisa
    // menghabiskan memori server.
    Route::get('/export', [DeveloperExportController::class, 'index'])->name('export');
    Route::get('/export/portfolio.pdf', [DeveloperExportController::class, 'pdf'])
        ->middleware('throttle:30,1')->name('export.pdf');   // render berat, tapi hasilnya di-cache
    Route::get('/export/pratinjau', [DeveloperExportController::class, 'pratinjau'])
        ->middleware('throttle:20,1')->name('export.preview');

    // Teks halaman login & loader, dan kerangka panel admin
    Route::get('/experiences',               [\App\Http\Controllers\Developer\DeveloperExperienceController::class, 'index'])->name('experiences');
    Route::post('/experiences',              [\App\Http\Controllers\Developer\DeveloperExperienceController::class, 'store'])->name('experiences.store');
    Route::put('/experiences/{id}',          [\App\Http\Controllers\Developer\DeveloperExperienceController::class, 'update'])->name('experiences.update');
    Route::post('/experiences/{id}/toggle',  [\App\Http\Controllers\Developer\DeveloperExperienceController::class, 'toggle'])->name('experiences.toggle');
    Route::delete('/experiences/{id}',       [\App\Http\Controllers\Developer\DeveloperExperienceController::class, 'destroy'])->name('experiences.destroy');
    Route::get('/tech-stacks',               [\App\Http\Controllers\Developer\DeveloperTechStackController::class, 'index'])->name('tech-stacks');
    Route::post('/tech-stacks',              [\App\Http\Controllers\Developer\DeveloperTechStackController::class, 'store'])->name('tech-stacks.store');
    Route::put('/tech-stacks/{id}',          [\App\Http\Controllers\Developer\DeveloperTechStackController::class, 'update'])->name('tech-stacks.update');
    Route::post('/tech-stacks/{id}/toggle',  [\App\Http\Controllers\Developer\DeveloperTechStackController::class, 'toggle'])->name('tech-stacks.toggle');
    Route::delete('/tech-stacks/{id}',       [\App\Http\Controllers\Developer\DeveloperTechStackController::class, 'destroy'])->name('tech-stacks.destroy');
    Route::get('/content', [DeveloperContentController::class, 'index'])->name('content');
    Route::post('/content', [DeveloperContentController::class, 'update'])->name('content.update');
    Route::get('/content/login-preview', [DeveloperContentController::class, 'loginPreview'])->name('login-preview');

    // Settings
    Route::get('/settings',  [DeveloperSettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [DeveloperSettingsController::class, 'update'])->name('settings.update');
});
