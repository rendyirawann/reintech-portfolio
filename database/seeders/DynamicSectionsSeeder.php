<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeroStat;
use App\Models\AboutCard;
use App\Models\ServiceItem;

class DynamicSectionsSeeder extends Seeder
{
    public function run(): void
    {
        // ── Hero Stats ────────────────────────────────
        HeroStat::truncate();
        $heroStats = [
            ['counter_value' => 24, 'suffix' => '+', 'label' => 'Projects',  'sort_order' => 1],
            ['counter_value' => 6,  'suffix' => '+', 'label' => 'Years Exp', 'sort_order' => 2],
            ['counter_value' => 18, 'suffix' => '+', 'label' => 'Clients',   'sort_order' => 3],
        ];
        foreach ($heroStats as $stat) {
            HeroStat::create($stat);
        }

        // ── About Cards ───────────────────────────────
        AboutCard::truncate();
        $aboutCards = [
            ['icon' => '🚀', 'title' => 'Builder',      'description' => 'I turn ideas into fast, beautiful, production-ready applications that real users love.',                             'sort_order' => 1],
            ['icon' => '🎨', 'title' => 'Designer-Dev',  'description' => 'Strong aesthetic sense combined with technical depth — I bridge design and engineering seamlessly.',                   'sort_order' => 2],
            ['icon' => '⚡', 'title' => 'Optimizer',     'description' => 'Performance-obsessed. Every millisecond and pixel matters in what I ship.',                                            'sort_order' => 3],
        ];
        foreach ($aboutCards as $card) {
            AboutCard::create($card);
        }

        // ── Service Items ─────────────────────────────
        ServiceItem::truncate();
        $serviceItems = [
            ['icon' => '💻', 'title' => 'Web Development',    'description' => 'Full-stack web apps built with Laravel, React, Vue.js — from MVP to production-grade enterprise systems.', 'price_text' => 'From IDR 5jt',  'sort_order' => 1],
            ['icon' => '📱', 'title' => 'Mobile Apps',         'description' => 'Cross-platform mobile applications with seamless UX across Android and iOS using modern frameworks.',     'price_text' => 'From IDR 8jt',  'sort_order' => 2],
            ['icon' => '🏛️', 'title' => 'Government Systems', 'description' => 'Specialized experience building secure, auditable e-government platforms with TTE, SPBE compliance.',    'price_text' => 'Custom Quote',  'sort_order' => 3],
            ['icon' => '🚀', 'title' => 'Tech Consulting',    'description' => 'Architecture review, stack selection, performance optimization, and DevOps setup for your team.',          'price_text' => 'IDR 500k/hr',   'sort_order' => 4],
        ];
        foreach ($serviceItems as $item) {
            ServiceItem::create($item);
        }
    }
}
