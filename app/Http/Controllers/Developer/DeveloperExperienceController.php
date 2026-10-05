<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;

class DeveloperExperienceController extends Controller
{
    public function index(Request $request)
    {
        $experiences = Experience::ordered()->get();
        $edit = $request->filled('edit') ? Experience::find($request->integer('edit')) : null;

        return view('developer.experiences', compact('experiences', 'edit'));
    }

    public function store(Request $request)
    {
        Experience::create($this->data($request));

        return redirect()->route('admin.experiences')->with('success', 'Riwayat ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        Experience::findOrFail($id)->update($this->data($request));

        return redirect()->route('admin.experiences')->with('success', 'Riwayat diperbarui.');
    }

    public function toggle(int $id)
    {
        $x = Experience::findOrFail($id);
        $x->update(['is_visible' => ! $x->is_visible]);

        return back()->with('success', $x->is_visible ? 'Riwayat ditampilkan.' : 'Riwayat disembunyikan.');
    }

    public function destroy(int $id)
    {
        Experience::findOrFail($id)->delete();

        return redirect()->route('admin.experiences')->with('success', 'Riwayat dihapus.');
    }

    private function data(Request $request): array
    {
        $v = $request->validate([
            'type'            => 'required|in:' . implode(',', array_keys(Experience::JENIS)),
            'title'           => 'required|string|max:160',
            'organization'    => 'required|string|max:160',
            'location'        => 'nullable|string|max:160',
            'employment_type' => 'nullable|string|max:60',
            'grade'           => 'nullable|string|max:40',
            'start_date'      => 'required|date_format:Y-m',
            'end_date'        => 'nullable|date_format:Y-m|after_or_equal:start_date',
            'is_current'      => 'nullable|boolean',
            'description'     => 'nullable|string|max:4000',
            'link_label'      => 'nullable|string|max:200',
            'link_url'        => 'nullable|url:http,https|max:500',
            'sort_order'      => 'nullable|integer|min:0|max:9999',
        ]);

        $v['is_current'] = $request->boolean('is_current');
        $v['start_date'] = $v['start_date'] . '-01';
        $v['end_date'] = $v['is_current'] || empty($v['end_date']) ? null : $v['end_date'] . '-01';
        $v['sort_order'] = $v['sort_order'] ?? 0;

        return $v;
    }
}
