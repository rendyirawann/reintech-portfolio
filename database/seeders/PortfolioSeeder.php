<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;
use App\Models\HeroData;
use App\Models\AboutSection;
use App\Models\AboutCard;
use App\Models\Project;
use App\Models\Service;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        // Site Settings
        SiteSetting::create(['key' => 'site_name', 'value' => 'Rein-Tech']);
        SiteSetting::create(['key' => 'logo_text', 'value' => 'RDev']);
        SiteSetting::create(['key' => 'footer_text', 'value' => '© 2024 Rein-Tech. All rights reserved.']);
        SiteSetting::create(['key' => 'open_to_work', 'value' => 'true']);

        // Hero Data
        HeroData::create([
            'badge_text' => '⚡ Available for freelance projects',
            'title' => 'I Build Digital Things That Matter',
            'description' => 'Full-Stack Developer & Tech Creator based in Indonesia. I craft high-performance web apps, mobile experiences, and digital solutions with a passion for clean code and stunning design.',
            'stats' => [
                ['label' => 'Projects Done', 'target' => 20],
                ['label' => 'Years Experience', 'target' => 3],
                ['label' => 'Happy Clients', 'target' => 15],
            ]
        ]);

        // About Section
        AboutSection::create([
            'tag' => 'The Visionary',
            'title' => 'Code, Design & Strategy Combined',
            'description' => 'I am a passionate software engineer who believes that every line of code should contribute to a meaningful user experience. With a background in both front-end aesthetics and back-end robustness, I bridge the gap between imagination and reality.'
        ]);

        // About Cards
        AboutCard::create(['icon' => '🎨', 'title' => 'Design Philosophy', 'content' => 'Minimalist, functional, and deeply rooted in user behavior analysis.', 'order' => 1]);
        AboutCard::create(['icon' => '⚙️', 'title' => 'Tech Stack', 'content' => 'Laravel, React, Tailwind, and high-performance cloud infrastructure.', 'order' => 2]);

        // Projects
        Project::create([
            'year' => '2024',
            'type' => 'SaaS Platform',
            'title' => 'Nexus Dashboard',
            'description' => 'A high-performance analytics dashboard for crypto traders with real-time data visualization.',
            'tags' => ['React', 'D3.js', 'Firebase'],
            'link' => '#',
            'order' => 1
        ]);
        Project::create([
            'year' => '2023',
            'type' => 'Mobile App',
            'title' => 'Pulse Health',
            'description' => 'AI-driven fitness companion that tracks biomechanics using smartphone cameras.',
            'tags' => ['Flutter', 'TensorFlow', 'Go'],
            'link' => '#',
            'order' => 2
        ]);

        // Services
        Service::create(['icon' => '🌐', 'title' => 'Web Development', 'description' => 'Custom web applications built with speed and scalability in mind.', 'price' => 'Starts from $1,200', 'order' => 1]);
        Service::create(['icon' => '📱', 'title' => 'Mobile Apps', 'description' => 'Native-like experiences for iOS and Android using cross-platform tools.', 'price' => 'Starts from $2,500', 'order' => 2]);
        Service::create(['icon' => '☁️', 'title' => 'Cloud Strategy', 'description' => 'Optimizing your infrastructure for cost and high availability.', 'price' => 'Consultation based', 'order' => 3]);
    }
}
