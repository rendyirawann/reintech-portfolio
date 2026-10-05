<?php

declare(strict_types=1);

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectImage;
use App\Models\ProjectLink;
use App\Services\MediaService;
use App\Support\Platform;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Pengelolaan proyek beserta tiga tabel anaknya: gambar, berkas, dan tautan.
 *
 * Seluruh penyimpanan berkas dilewatkan MediaService — tidak ada `->store()`
 * langsung di sini. Itu yang membuat kompresi, penamaan ulang, dan batas
 * ukuran berlaku sama di setiap jalur unggah.
 */
class DeveloperProjectController extends Controller
{
    /** Batas ukuran unggahan dalam kilobyte (2 MB). */
    private const MAKS_KB = 2048;

    public function __construct(private readonly MediaService $media)
    {
    }

    public function index(): View
    {
        // withCount, bukan with(): halaman daftar hanya menampilkan JUMLAH
        // gambar/berkas/tautan. Memuat seluruh barisnya berarti ratusan baris
        // ikut terbaca hanya untuk dihitung.
        $projects = Project::query()
            ->withCount(['images', 'files', 'links'])
            ->orderBy('sort_order')
            ->orderByDesc('year')
            ->get();

        return view('developer.projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('developer.projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->aturan());

        $project = Project::create($this->siapkan($data, $request));

        $this->simpanGambar($request, $project);
        $this->simpanBerkas($request, $project);
        $this->simpanTautan($request, $project);

        return redirect()
            ->route('admin.projects.edit', $project->id)
            ->with('success', 'Proyek dibuat. Gambar, berkas, dan tautan bisa ditambah lagi di bawah.');
    }

    public function edit(int|string $id): View
    {
        // Ketiga relasi dimuat sekaligus — tanpa ini setiap kartu di halaman
        // memicu kuerinya sendiri.
        $project = Project::with(['images', 'files', 'links'])->findOrFail($id);

        return view('developer.projects.edit', compact('project'));
    }

    public function update(Request $request, int|string $id): RedirectResponse
    {
        $project = Project::findOrFail($id);

        $data = $request->validate($this->aturan());
        $siap = $this->siapkan($data, $request, $project);

        $project->update($siap);

        $this->simpanGambar($request, $project);
        $this->simpanBerkas($request, $project);
        $this->simpanTautan($request, $project);

        return redirect()
            ->route('admin.projects.edit', $project->id)
            ->with('success', 'Proyek diperbarui.');
    }

    public function destroy(int|string $id): RedirectResponse
    {
        $project = Project::with(['images', 'files'])->findOrFail($id);

        // Baris anaknya ikut terhapus oleh foreign key cascade, tetapi
        // BERKASNYA tidak — itu harus dibereskan di sini, kalau tidak disk
        // akan penuh oleh berkas yatim yang tidak bisa dilacak lagi.
        $this->media->hapus($project->main_image);
        foreach ($project->images as $image) {
            $this->media->hapus($image->image_path);
        }
        foreach ($project->files as $file) {
            $this->media->hapus($file->file_path);
        }

        $project->delete();

        return redirect()->route('admin.projects')->with('success', 'Proyek dihapus.');
    }

    public function toggle(int|string $id): RedirectResponse
    {
        $project = Project::findOrFail($id);
        $project->update(['is_visible' => ! $project->is_visible]);

        return back()->with('success', 'Status tampil proyek diubah.');
    }

    // ───────────────────────── Gambar (anak) ─────────────────────────

    public function uploadImages(Request $request, int|string $id): RedirectResponse
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'images'   => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,webp,gif|max:' . self::MAKS_KB,
        ]);

        $this->simpanGambar($request, $project);

        return back()->with('success', 'Gambar ditambahkan.');
    }

    public function destroyImage(int|string $imageId): RedirectResponse
    {
        $image = ProjectImage::findOrFail($imageId);
        $this->media->hapus($image->image_path);
        $image->delete();

        return back()->with('success', 'Gambar dihapus.');
    }

    // ───────────────────────── Berkas (anak) ─────────────────────────

    public function uploadFiles(Request $request, int|string $id): RedirectResponse
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'files'   => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf,zip,doc,docx,xls,xlsx,txt|max:' . self::MAKS_KB,
        ]);

        $this->simpanBerkas($request, $project);

        return back()->with('success', 'Berkas ditambahkan.');
    }

    public function destroyFile(int|string $fileId): RedirectResponse
    {
        $file = ProjectFile::findOrFail($fileId);
        $this->media->hapus($file->file_path);
        $file->delete();

        return back()->with('success', 'Berkas dihapus.');
    }

    // ───────────────────────── Tautan (anak) ─────────────────────────

    public function storeLink(Request $request, int|string $id): RedirectResponse
    {
        $project = Project::findOrFail($id);

        $data = $request->validate([
            'label' => 'required|string|max:100',
            'url'   => 'required|url|max:500',
        ]);

        $project->links()->create($data + [
            'sort_order' => (int) $project->links()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Tautan ditambahkan.');
    }

    public function destroyLink(int|string $linkId): RedirectResponse
    {
        ProjectLink::findOrFail($linkId)->delete();

        return back()->with('success', 'Tautan dihapus.');
    }

    // ───────────────────────────── Pembantu ─────────────────────────────

    /** @return array<string, string> */
    private function aturan(): array
    {
        return [
            'title'             => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'long_description'  => 'nullable|string|max:20000',
            'main_image'        => 'nullable|image|mimes:jpeg,png,webp,gif|max:' . self::MAKS_KB,
            'category'          => 'nullable|string|max:100',
            'year'              => 'nullable|integer|min:1990|max:2100',
            'live_url'          => 'nullable|url|max:500',
            'repo_url'          => 'nullable|url|max:500',
            'tech_stack'        => 'nullable|string|max:1000',
            'sort_order'        => 'required|integer|min:0|max:9999',

            // Anak-anak, semuanya opsional supaya satu formulir bisa dipakai
            // untuk membuat proyek lengkap sekaligus.
            'images'            => 'nullable|array',
            'images.*'          => 'image|mimes:jpeg,png,webp,gif|max:' . self::MAKS_KB,
            'files'             => 'nullable|array',
            'files.*'           => 'file|mimes:pdf,zip,doc,docx,xls,xlsx,txt|max:' . self::MAKS_KB,
            'link_label'        => 'nullable|array',
            'link_label.*'      => 'nullable|string|max:100',
            'link_url'          => 'nullable|array',
            'link_url.*'        => 'nullable|url|max:500',
        ];
    }

    /**
     * Ubah masukan mentah menjadi kolom yang siap disimpan.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function siapkan(array $data, Request $request, ?Project $project = null): array
    {
        $kolom = array_intersect_key($data, array_flip([
            'title', 'short_description', 'long_description',
            'category', 'year', 'live_url', 'repo_url', 'sort_order',
        ]));

        // Daftar teknologi dikirim sebagai teks berkoma; yang kosong dibuang
        // supaya "Laravel, , Vue" tidak menghasilkan cip kosong di halaman.
        $kolom['tech_stack'] = collect(explode(',', (string) ($data['tech_stack'] ?? '')))
            ->map(static fn (string $t): string => trim($t))
            ->filter()
            ->values()
            ->all();

        $kolom['slug'] = $this->slugUnik($data['title'], $project?->id);

        if ($request->hasFile('main_image')) {
            $this->media->hapus($project?->main_image);
            $kolom['main_image'] = $this->media->simpanGambar(
                $request->file('main_image'),
                'portfolio/projects'
            );
        }

        return $kolom;
    }

    /**
     * Slug yang dijamin tidak bentrok.
     *
     * Dua proyek boleh berjudul sama — "Redesign" tahun lalu dan tahun ini —
     * dan tanpa penjagaan di sini yang kedua akan gagal menyentuh unique index
     * dengan pesan galat basis data mentah.
     */
    private function slugUnik(string $judul, int|string|null $abaikanId = null): string
    {
        $dasar = Str::slug($judul) ?: 'proyek';
        $slug = $dasar;
        $n = 2;

        while (
            Project::where('slug', $slug)
                ->when($abaikanId, static fn ($q) => $q->whereKeyNot($abaikanId))
                ->exists()
        ) {
            $slug = $dasar . '-' . $n++;
        }

        return $slug;
    }

    private function simpanGambar(Request $request, Project $project): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $urut = (int) $project->images()->max('sort_order');

        foreach ($request->file('images') as $file) {
            $project->images()->create([
                'image_path' => $this->media->simpanGambar($file, 'portfolio/projects/gallery'),
                'sort_order' => ++$urut,
            ]);
        }
    }

    private function simpanBerkas(Request $request, Project $project): void
    {
        if (! $request->hasFile('files')) {
            return;
        }

        $urut = (int) $project->files()->max('sort_order');

        foreach ($request->file('files') as $file) {
            $project->files()->create(
                $this->media->simpanBerkas($file, 'portfolio/projects/files') + ['sort_order' => ++$urut]
            );
        }
    }

    private function simpanTautan(Request $request, Project $project): void
    {
        $label = $request->input('link_label', []);
        $url = $request->input('link_url', []);

        if (! is_array($url) || $url === []) {
            return;
        }

        $urut = (int) $project->links()->max('sort_order');

        foreach ($url as $i => $alamat) {
            if (blank($alamat)) {
                continue;
            }

            $project->links()->create([
                // Label boleh kosong; kalau begitu nama platformnya dipakai,
                // jadi tombolnya tidak pernah tampil tanpa teks.
                'label'      => trim((string) ($label[$i] ?? '')) ?: Platform::label(Platform::dariUrl($alamat)),
                'url'        => $alamat,
                'sort_order' => ++$urut,
            ]);
        }
    }
}
