<?php

namespace App\Http\Controllers\Settings;

use Inertia\Inertia;
use App\Models\Appearance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppearanceController extends Controller
{
    public function index(Request $request)
    {
        $lastAppearance = Appearance::latest()->first();

        return Inertia::render('settings/Appearance', [
            'edited_by' => $lastAppearance?->user->name,
            'updated_at' => $lastAppearance?->updated_at->format('d-m-Y H:i:s'),
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'name_app' => 'nullable|string|max:255',
        'icon_app' => 'nullable|image|mimes:jpg,jpeg,png,ico|max:2048',
        'logo_app' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $lastAppearance = Appearance::latest()->first();

    if ($request->hasFile('icon_app')) {
        $iconPath = $request->file('icon_app')->store('icons', 'public');
    } else {
        $iconPath = $lastAppearance?->icon_app; 
    }

    if ($request->hasFile('logo_app')) {
        $logoPath = $request->file('logo_app')->store('logos', 'public');
    } else {
        $logoPath = $lastAppearance?->logo_app; 
    }

    Appearance::create([
        'edited_by' => $request->user()->id,
        'name_app' => $request->input('name_app'), 
        'icon_app' => $iconPath,
        'logo_app' => $logoPath,
    ]);

    return redirect()->back()->with('success', 'Appearance updated successfully.');
}
}
