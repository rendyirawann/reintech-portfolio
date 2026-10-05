<?php

declare(strict_types=1);

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\PortfolioIdentity;
use App\Models\PortfolioSection;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectImage;
use App\Models\SocialLink;
use Illuminate\View\View;

class DeveloperDashboardController extends Controller
{
    public function index(): View
    {
        // Hitungan proyek diambil dalam SATU kueri dengan FILTER, bukan dua
        // count() terpisah — sebelumnya total dan yang tampil dibaca dua kali
        // dari tabel yang sama.
        $proyek = Project::query()
            ->selectRaw('count(*) as total, count(*) filter (where is_visible) as tampil')
            ->first();

        $stats = [
            'projects' => (int) $proyek->total,
            'visible'  => (int) $proyek->tampil,
            'media'    => ProjectImage::count() + ProjectFile::count(),
            'socials'  => SocialLink::where('is_visible', true)->count(),
            'sections' => PortfolioSection::where('is_visible', true)->count(),
            'unread'   => ContactMessage::unread()->count(),
        ];

        // Hanya kolom yang ditampilkan. Kartu proyek terbaru tidak butuh
        // long_description yang bisa ribuan karakter.
        $terbaru = Project::query()
            ->select(['id', 'title', 'category', 'year', 'main_image', 'is_visible', 'updated_at'])
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $identity = PortfolioIdentity::instance();

        // Kelengkapan profil: yang dibutuhkan dokumen ekspor dan SEO. Ditampilkan
        // supaya kekosongan terlihat SEBELUM portofolionya diunduh, bukan sesudah.
        $syarat = [
            'Foto profil'      => filled($identity->profile_image),
            'Nama lengkap'     => filled($identity->full_name),
            'Headline'         => filled($identity->headline),
            'Email kontak'     => filled($identity->contact_email),
            'Deskripsi SEO'    => filled($identity->meta_description),
            'Minimal 1 proyek' => $stats['visible'] > 0,
        ];

        return view('developer.dashboard', compact('stats', 'terbaru', 'identity', 'syarat'));
    }
}
