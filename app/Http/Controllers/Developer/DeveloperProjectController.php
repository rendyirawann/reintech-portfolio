<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeveloperProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('sort_order')->orderByDesc('year')->get();
        return view('developer.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('developer.projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'long_description'  => 'nullable|string',
            'main_image'        => 'nullable|image|max:2048',
            'category'          => 'nullable|string|max:100',
            'year'              => 'nullable|integer',
            'live_url'          => 'nullable|url|max:500',
            'repo_url'          => 'nullable|url|max:500',
            'tech_stack'        => 'nullable|string', // comma separated
            'sort_order'        => 'required|integer',
        ]);

        if ($request->hasFile('main_image')) {
            $validated['main_image'] = $request->file('main_image')->store('portfolio/projects', 'public');
        }

        if ($validated['tech_stack']) {
            $validated['tech_stack'] = array_map('trim', explode(',', $validated['tech_stack']));
        } else {
            $validated['tech_stack'] = [];
        }

        $validated['slug'] = Str::slug($validated['title']);

        Project::create($validated);

        return redirect()->route('developer.projects')->with('success', 'Project created successfully.');
    }

    public function edit($id)
    {
        $project = Project::with('images')->findOrFail($id);
        return view('developer.projects.edit', compact('project'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'long_description'  => 'nullable|string',
            'main_image'        => 'nullable|image|max:2048',
            'category'          => 'nullable|string|max:100',
            'year'              => 'nullable|integer',
            'live_url'          => 'nullable|url|max:500',
            'repo_url'          => 'nullable|url|max:500',
            'tech_stack'        => 'nullable|string',
            'sort_order'        => 'required|integer',
        ]);

        if ($request->hasFile('main_image')) {
            if ($project->main_image) {
                Storage::disk('public')->delete($project->main_image);
            }
            $validated['main_image'] = $request->file('main_image')->store('portfolio/projects', 'public');
        }

        if ($validated['tech_stack'] !== null) {
            $validated['tech_stack'] = array_map('trim', explode(',', $validated['tech_stack']));
        } else {
            $validated['tech_stack'] = [];
        }

        $validated['slug'] = Str::slug($validated['title']);

        $project->update($validated);

        return redirect()->route('developer.projects.edit', $id)->with('success', 'Project updated successfully.');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        
        if ($project->main_image) {
            Storage::disk('public')->delete($project->main_image);
        }

        foreach ($project->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $project->delete();

        return redirect()->route('developer.projects')->with('success', 'Project deleted.');
    }

    public function toggle($id)
    {
        $project = Project::findOrFail($id);
        $project->update(['is_visible' => !$project->is_visible]);
        return back()->with('success', 'Project visibility toggled.');
    }

    // --- Child Images ---

    public function uploadImages(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'images.*' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('portfolio/projects/gallery', 'public');
                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $path,
                ]);
            }
        }

        return back()->with('success', 'Images uploaded successfully.');
    }

    public function destroyImage($imageId)
    {
        $image = ProjectImage::findOrFail($imageId);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Image deleted.');
    }
}
