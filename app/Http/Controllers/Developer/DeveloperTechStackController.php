<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\TechStack;
use App\Support\TechIcon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeveloperTechStackController extends Controller
{
    public function index()
    {
        return view('developer.tech-stacks', ['stacks' => TechStack::ordered()->get()]);
    }

    public function store(Request $request)
    {
        $v = $this->data($request);
        $pesan = TechIcon::unduh($v['slug']) ? 'Teknologi ditambahkan.' : 'Teknologi ditambahkan, tetapi ikonnya tidak ditemukan — tampil sebagai inisial.';
        TechStack::create($v);

        return redirect()->route('admin.tech-stacks')->with('success', $pesan);
    }

    public function update(Request $request, int $id)
    {
        $v = $this->data($request);
        TechIcon::unduh($v['slug']);
        TechStack::findOrFail($id)->update($v);

        return redirect()->route('admin.tech-stacks')->with('success', 'Teknologi diperbarui.');
    }

    public function toggle(int $id)
    {
        $t = TechStack::findOrFail($id);
        $t->update(['is_visible' => ! $t->is_visible]);

        return back()->with('success', $t->is_visible ? 'Ditampilkan.' : 'Disembunyikan.');
    }

    public function destroy(int $id)
    {
        TechStack::findOrFail($id)->delete();

        return redirect()->route('admin.tech-stacks')->with('success', 'Teknologi dihapus.');
    }

    private function data(Request $request): array
    {
        $v = $request->validate([
            'name'       => 'required|string|max:60',
            'slug'       => 'nullable|string|max:60|regex:/^[a-z0-9]+$/',
            'color'      => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'group'      => 'required|in:' . implode(',', TechStack::KELOMPOK),
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ], ['slug.regex' => 'Kode ikon hanya huruf kecil dan angka, mis. laravel, postgresql.']);

        // Kode ikon kosong → tebak dari nama ("Tailwind CSS" → "tailwindcss").
        $v['slug'] = $v['slug'] ?: Str::of($v['name'])->lower()->replaceMatches('/[^a-z0-9]/', '')->toString();
        $v['sort_order'] = $v['sort_order'] ?? 0;

        return $v;
    }
}
