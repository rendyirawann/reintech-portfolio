<?php

namespace App\Http\Controllers;

use App\Models\PortfolioIdentity;
use App\Models\PortfolioSection;
use App\Models\Project;
use App\Models\SocialLink;
use App\Models\HeroStat;
use App\Models\AboutCard;
use App\Models\ServiceItem;
use App\Models\Experience;
use App\Models\TechStack;

class WelcomeController extends Controller
{
    public function index()
    {
        $identity     = PortfolioIdentity::instance();
        $socials      = SocialLink::visible()->ordered()->get();
        $sections     = PortfolioSection::all()->keyBy('section_key');
        // Semua proyek hanya untuk statistik (kolom kategori saja); kartu yang
        // dirender cukup satu halaman (4) — sisanya diambil lewat AJAX.
        $projects     = Project::visible()->get(['id', 'category']);
        $daftarProyek = ProjectListController::cari(request('q'), (int) request('proyek', 1));
        $heroStats    = HeroStat::visible()->ordered()->get();
        $aboutCards   = AboutCard::visible()->ordered()->get();
        $serviceItems = ServiceItem::visible()->ordered()->get();
        $experiences  = Experience::visible()->ordered()->get();
        $techStacks   = TechStack::visible()->ordered()->get();
        $pengalamanSejak = Experience::visible()->where('type', 'work')->min('start_date');
        $pengalamanSejak = $pengalamanSejak ? substr($pengalamanSejak, 0, 4) : null;

        return view('welcome', compact(
            'identity', 'socials', 'sections', 'projects',
            'heroStats', 'aboutCards', 'serviceItems',
            'experiences', 'techStacks', 'pengalamanSejak', 'daftarProyek'
        ));
    }
}
