<?php

namespace App\Http\Controllers;

use App\Models\PortfolioIdentity;
use App\Models\PortfolioSection;
use App\Models\Project;
use App\Models\SocialLink;
use App\Models\HeroStat;
use App\Models\AboutCard;
use App\Models\ServiceItem;

class WelcomeController extends Controller
{
    public function index()
    {
        $identity     = PortfolioIdentity::instance();
        $socials      = SocialLink::visible()->ordered()->get();
        $sections     = PortfolioSection::all()->keyBy('section_key');
        $projects     = Project::visible()->ordered()->with('images')->get();
        $heroStats    = HeroStat::visible()->ordered()->get();
        $aboutCards   = AboutCard::visible()->ordered()->get();
        $serviceItems = ServiceItem::visible()->ordered()->get();

        return view('welcome', compact(
            'identity', 'socials', 'sections', 'projects',
            'heroStats', 'aboutCards', 'serviceItems'
        ));
    }
}
