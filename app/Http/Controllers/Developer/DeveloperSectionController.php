<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\PortfolioSection;
use App\Models\PortfolioIdentity;
use App\Models\HeroStat;
use App\Models\AboutCard;
use App\Models\ServiceItem;
use Illuminate\Http\Request;

class DeveloperSectionController extends Controller
{
    public function index()
    {
        $sections     = PortfolioSection::orderBy('id')->get();
        $identity     = PortfolioIdentity::instance();
        $heroStats    = HeroStat::ordered()->get();
        $aboutCards   = AboutCard::ordered()->get();
        $serviceItems = ServiceItem::ordered()->get();

        return view('developer.sections', compact(
            'sections', 'identity', 'heroStats', 'aboutCards', 'serviceItems'
        ));
    }

    public function update(Request $request, $id)
    {
        $section = PortfolioSection::findOrFail($id);

        $validated = $request->validate([
            'title'            => 'nullable|string|max:255',
            'subtitle'         => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'contact_email'    => 'nullable|string|email|max:100',
            'contact_whatsapp' => 'nullable|string|max:50',
        ]);

        $section->update([
            'title'       => $validated['title'],
            'subtitle'    => $validated['subtitle'],
            'description' => $validated['description'],
        ]);

        if ($section->section_key === 'contact') {
            $identity = PortfolioIdentity::instance();
            $identity->update([
                'contact_email'    => $validated['contact_email'] ?? $identity->contact_email,
                'contact_whatsapp' => $validated['contact_whatsapp'] ?? $identity->contact_whatsapp,
            ]);
        }

        return redirect()->route('developer.sections')->with('success', 'Section updated successfully.');
    }

    public function toggle($id)
    {
        $section = PortfolioSection::findOrFail($id);
        $section->update(['is_visible' => !$section->is_visible]);
        return back()->with('success', 'Section visibility toggled.');
    }

    // ── Hero Stats CRUD ────────────────────────────────

    public function storeHeroStat(Request $request)
    {
        $validated = $request->validate([
            'counter_value' => 'required|integer|min:0',
            'suffix'        => 'required|string|max:10',
            'label'         => 'required|string|max:50',
            'sort_order'    => 'required|integer',
        ]);

        HeroStat::create($validated);
        return redirect()->route('developer.sections')->with('success', 'Hero stat added.');
    }

    public function updateHeroStat(Request $request, $id)
    {
        $stat = HeroStat::findOrFail($id);
        $validated = $request->validate([
            'counter_value' => 'required|integer|min:0',
            'suffix'        => 'required|string|max:10',
            'label'         => 'required|string|max:50',
            'sort_order'    => 'required|integer',
        ]);
        $stat->update($validated);
        return redirect()->route('developer.sections')->with('success', 'Hero stat updated.');
    }

    public function destroyHeroStat($id)
    {
        HeroStat::findOrFail($id)->delete();
        return redirect()->route('developer.sections')->with('success', 'Hero stat deleted.');
    }

    // ── About Cards CRUD ───────────────────────────────

    public function storeAboutCard(Request $request)
    {
        $validated = $request->validate([
            'icon'        => 'required|string|max:20',
            'title'       => 'required|string|max:100',
            'description' => 'required|string',
            'sort_order'  => 'required|integer',
        ]);

        AboutCard::create($validated);
        return redirect()->route('developer.sections')->with('success', 'About card added.');
    }

    public function updateAboutCard(Request $request, $id)
    {
        $card = AboutCard::findOrFail($id);
        $validated = $request->validate([
            'icon'        => 'required|string|max:20',
            'title'       => 'required|string|max:100',
            'description' => 'required|string',
            'sort_order'  => 'required|integer',
        ]);
        $card->update($validated);
        return redirect()->route('developer.sections')->with('success', 'About card updated.');
    }

    public function destroyAboutCard($id)
    {
        AboutCard::findOrFail($id)->delete();
        return redirect()->route('developer.sections')->with('success', 'About card deleted.');
    }

    // ── Service Items CRUD ─────────────────────────────

    public function storeServiceItem(Request $request)
    {
        $validated = $request->validate([
            'icon'        => 'required|string|max:20',
            'title'       => 'required|string|max:100',
            'description' => 'required|string',
            'price_text'  => 'nullable|string|max:100',
            'sort_order'  => 'required|integer',
        ]);

        ServiceItem::create($validated);
        return redirect()->route('developer.sections')->with('success', 'Service item added.');
    }

    public function updateServiceItem(Request $request, $id)
    {
        $item = ServiceItem::findOrFail($id);
        $validated = $request->validate([
            'icon'        => 'required|string|max:20',
            'title'       => 'required|string|max:100',
            'description' => 'required|string',
            'price_text'  => 'nullable|string|max:100',
            'sort_order'  => 'required|integer',
        ]);
        $item->update($validated);
        return redirect()->route('developer.sections')->with('success', 'Service item updated.');
    }

    public function destroyServiceItem($id)
    {
        ServiceItem::findOrFail($id)->delete();
        return redirect()->route('developer.sections')->with('success', 'Service item deleted.');
    }
}
