<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Developer\DeveloperAuthController;
use App\Http\Controllers\Developer\DeveloperDashboardController;
use App\Http\Controllers\Developer\DeveloperIdentityController;
use App\Http\Controllers\Developer\DeveloperSectionController;
use App\Http\Controllers\Developer\DeveloperProjectController;
use App\Http\Controllers\Developer\DeveloperSettingsController;

// ─── PUBLIC ────────────────────────────────────────────────
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// ─── DEVELOPER AUTH (no middleware) ────────────────────────
Route::prefix('developer-access/auth')->name('developer.')->group(function () {
    Route::get('/login',  [DeveloperAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [DeveloperAuthController::class, 'login'])->name('login.post');
    Route::post('/logout',[DeveloperAuthController::class, 'logout'])->name('logout');
});

// ─── DEVELOPER PANEL (protected) ───────────────────────────
Route::prefix('developer-access')->name('developer.')->middleware('developer.auth')->group(function () {
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

    // Project Images (child)
    Route::post('/projects/{id}/images',          [DeveloperProjectController::class, 'uploadImages'])->name('projects.images.upload');
    Route::delete('/projects/images/{imageId}',   [DeveloperProjectController::class, 'destroyImage'])->name('projects.images.destroy');

    // Settings
    Route::get('/settings',  [DeveloperSettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [DeveloperSettingsController::class, 'update'])->name('settings.update');
});
