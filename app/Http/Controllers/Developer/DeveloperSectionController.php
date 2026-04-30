<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\PortfolioSection;
use Illuminate\Http\Request;

class DeveloperSectionController extends Controller
{
    public function index()
    {
        $sections = PortfolioSection::orderBy('id')->get();
        return view('developer.sections', compact('sections'));
    }

    public function update(Request $request, $id)
    {
        $section = PortfolioSection::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $section->update($validated);

        return redirect()->route('developer.sections')->with('success', 'Section updated successfully.');
    }

    public function toggle($id)
    {
        $section = PortfolioSection::findOrFail($id);
        $section->update(['is_visible' => !$section->is_visible]);
        return back()->with('success', 'Section visibility toggled.');
    }
}
