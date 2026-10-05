<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Daftar proyek halaman depan: 4 per halaman, bisa dicari.
 * Halaman pertama dirender WelcomeController; sisanya diambil lewat AJAX.
 */
class ProjectListController extends Controller
{
    public const PER_HALAMAN = 4;

    public static function cari(?string $q, int $halaman = 1): LengthAwarePaginator
    {
        $q = trim(mb_substr((string) $q, 0, 80));

        return Project::visible()->ordered()
            ->with('images:id,project_id,image_path')
            ->when($q !== '', function ($query) use ($q) {
                // Parameter terikat; % dan _ dari pengguna di-escape agar tidak jadi wildcard.
                $pola = '%' . addcslashes(mb_strtolower($q), '%_\\') . '%';
                $query->where(fn ($w) => $w
                    ->whereRaw('LOWER(title) LIKE ?', [$pola])
                    ->orWhereRaw('LOWER(category) LIKE ?', [$pola])
                    ->orWhereRaw('LOWER(short_description) LIKE ?', [$pola])
                    ->orWhereRaw('LOWER(CAST(tech_stack AS TEXT)) LIKE ?', [$pola]));
            })
            ->paginate(self::PER_HALAMAN, ['*'], 'proyek', max(1, $halaman))
            ->withPath(route('home'))
            ->appends(array_filter(['q' => $q]));
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'q' => 'nullable|string|max:80',
            'proyek' => 'nullable|integer|min:1|max:1000',
        ]);

        $p = self::cari($data['q'] ?? null, (int) ($data['proyek'] ?? 1));

        $html = $p->getCollection()
            ->map(fn ($project) => view('partials.project-card', compact('project'))->render())
            ->implode('');

        return response()->json([
            'html' => $html ?: '<p class="proyek-kosong">Tidak ada proyek yang cocok.</p>',
            'pager' => view('partials.project-pager', ['p' => $p])->render(),
            'total' => $p->total(),
            'page' => $p->currentPage(),
        ])->header('Cache-Control', 'public, max-age=60');
    }
}
