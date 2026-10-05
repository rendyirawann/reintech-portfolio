<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\PortfolioIdentity;
use App\Services\MediaService;
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

    public function __construct(private readonly MediaService $media)
    {
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
            'contact_email'      => 'nullable|string|email|max:100',
            'contact_whatsapp'   => 'nullable|string|max:50',
            'contact_linkedin'   => 'nullable|url|max:500',
            'footer_text'        => 'nullable|string|max:255',

            // Data diri — dipakai bagian Tentang dan dokumen ekspor
            'full_name'          => 'nullable|string|max:120',
            'headline'           => 'nullable|string|max:160',
            'summary'            => 'nullable|string|max:3000',
            'languages'          => 'nullable|string|max:255',
            'location'           => 'nullable|string|max:120',
            'profile_image'      => 'nullable|image|mimes:jpeg,png,webp|max:2048',

            // SEO
            'meta_title'         => 'nullable|string|max:255',
            'meta_description'   => 'nullable|string|max:320',
            'meta_keywords'      => 'nullable|string|max:500',
            'og_image'           => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $identity->fill([
            'logo_text'          => $validated['logo_text'],
            'logo_subtext'       => $validated['logo_subtext'],
            'topbar_status_text' => $validated['topbar_status_text'],
            'topbar_role_text'   => $validated['topbar_role_text'],
            'sidebar_icon_type'  => $validated['sidebar_icon_type'],
            'contact_email'      => $validated['contact_email'],
            'contact_whatsapp'   => (string) ($validated['contact_whatsapp'] ?? ''),
            'footer_text'        => $validated['footer_text'],
            'contact_linkedin'   => $validated['contact_linkedin'] ?? null,
            'full_name'          => $validated['full_name'] ?? null,
            'headline'           => $validated['headline'] ?? null,
            'summary'            => $validated['summary'] ?? null,
            'languages'          => $validated['languages'] ?? null,
            'location'           => $validated['location'] ?? null,
            'meta_title'         => $validated['meta_title'] ?? null,
            'meta_description'   => $validated['meta_description'] ?? null,
            'meta_keywords'      => $validated['meta_keywords'] ?? null,
        ]);

        // Kedua gambar lewat MediaService: terkompres, dan nama berkasnya
        // dibangkitkan ulang sehingga tidak bisa ditentukan pengunggah.
        foreach (['profile_image', 'og_image'] as $kolom) {
            if ($request->hasFile($kolom)) {
                $this->media->hapus($identity->{$kolom});
                $identity->{$kolom} = $this->media->simpanGambar(
                    $request->file($kolom),
                    'portfolio/identity'
                );
            }
        }

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

        return redirect()->route('admin.identity')->with('success', 'Identity updated successfully.');
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
            'url'        => 'required|string|max:500',
            // Tidak lagi wajib: ikon dirakit dari platform hasil pengenalan URL.
            // Kolomnya dipertahankan agar data lama tidak perlu dimigrasi.
            'icon_svg'   => 'nullable|string|max:5000',
            'sort_order' => 'required|integer',
        ]);

        SocialLink::create($validated);

        return redirect()->route('admin.identity.socials')->with('success', 'Social link added.');
    }

    public function updateSocial(Request $request, $id)
    {
        $social = SocialLink::findOrFail($id);

        $validated = $request->validate([
            'platform'   => 'required|string|max:50',
            'label'      => 'required|string|max:100',
            'url'        => 'required|string|max:500',
            // Tidak lagi wajib: ikon dirakit dari platform hasil pengenalan URL.
            // Kolomnya dipertahankan agar data lama tidak perlu dimigrasi.
            'icon_svg'   => 'nullable|string|max:5000',
            'sort_order' => 'required|integer',
        ]);

        $social->update($validated);

        return redirect()->route('admin.identity.socials')->with('success', 'Social link updated.');
    }

    public function destroySocial($id)
    {
        SocialLink::findOrFail($id)->delete();
        return redirect()->route('admin.identity.socials')->with('success', 'Social link deleted.');
    }

    public function toggleSocial($id)
    {
        $social = SocialLink::findOrFail($id);
        $social->update(['is_visible' => !$social->is_visible]);
        return back()->with('success', 'Visibility toggled.');
    }
}
