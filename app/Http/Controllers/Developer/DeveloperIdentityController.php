<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\PortfolioIdentity;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeveloperIdentityController extends Controller
{
    public function index()
    {
        $identity = PortfolioIdentity::instance();
        return view('developer.identity', compact('identity'));
    }

    public function update(Request $request)
    {
        $identity = PortfolioIdentity::instance();

        $validated = $request->validate([
            'logo_text'          => 'required|string|max:10',
            'logo_subtext'       => 'nullable|string|max:50',
            'topbar_status_text' => 'nullable|string|max:100',
            'topbar_role_text'   => 'nullable|string|max:100',
            'sidebar_icon_type'  => 'required|in:text,image',
            'sidebar_icon_value_text' => 'required_if:sidebar_icon_type,text|string|max:10',
            'sidebar_icon_image' => 'required_if:sidebar_icon_type,image|image|max:2048',
        ]);

        $identity->fill([
            'logo_text'          => $validated['logo_text'],
            'logo_subtext'       => $validated['logo_subtext'],
            'topbar_status_text' => $validated['topbar_status_text'],
            'topbar_role_text'   => $validated['topbar_role_text'],
            'sidebar_icon_type'  => $validated['sidebar_icon_type'],
        ]);

        if ($validated['sidebar_icon_type'] === 'text') {
            $identity->sidebar_icon_value = $validated['sidebar_icon_value_text'];
        } elseif ($request->hasFile('sidebar_icon_image')) {
            if ($identity->sidebar_icon_type === 'image' && $identity->sidebar_icon_value) {
                Storage::disk('public')->delete($identity->sidebar_icon_value);
            }
            $path = $request->file('sidebar_icon_image')->store('portfolio/identity', 'public');
            $identity->sidebar_icon_value = $path;
        }

        $identity->save();

        return redirect()->route('developer.identity')->with('success', 'Identity updated successfully.');
    }

    // --- Social Links ---

    public function socials()
    {
        $socials = SocialLink::orderBy('sort_order')->get();
        return view('developer.socials', compact('socials'));
    }

    public function storeSocial(Request $request)
    {
        $validated = $request->validate([
            'platform'   => 'required|string|max:50',
            'label'      => 'required|string|max:100',
            'url'        => 'required|url|max:500',
            'icon_svg'   => 'required|string',
            'sort_order' => 'required|integer',
        ]);

        SocialLink::create($validated);

        return redirect()->route('developer.identity.socials')->with('success', 'Social link added.');
    }

    public function updateSocial(Request $request, $id)
    {
        $social = SocialLink::findOrFail($id);

        $validated = $request->validate([
            'platform'   => 'required|string|max:50',
            'label'      => 'required|string|max:100',
            'url'        => 'required|url|max:500',
            'icon_svg'   => 'required|string',
            'sort_order' => 'required|integer',
        ]);

        $social->update($validated);

        return redirect()->route('developer.identity.socials')->with('success', 'Social link updated.');
    }

    public function destroySocial($id)
    {
        SocialLink::findOrFail($id)->delete();
        return redirect()->route('developer.identity.socials')->with('success', 'Social link deleted.');
    }

    public function toggleSocial($id)
    {
        $social = SocialLink::findOrFail($id);
        $social->update(['is_visible' => !$social->is_visible]);
        return back()->with('success', 'Visibility toggled.');
    }
}
