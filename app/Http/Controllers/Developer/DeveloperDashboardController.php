<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SocialLink;
use App\Models\PortfolioSection;

class DeveloperDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'projects'  => Project::count(),
            'visible'   => Project::where('is_visible', true)->count(),
            'socials'   => SocialLink::where('is_visible', true)->count(),
            'sections'  => PortfolioSection::where('is_visible', true)->count(),
        ];

        return view('developer.dashboard', compact('stats'));
    }
}
