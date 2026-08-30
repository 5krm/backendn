<?php

namespace App\Http\Controllers\Auth;

use App\Data\ListItemData;
use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompleteProfileController extends Controller
{
    public function show(): View
    {
        $language = app()->getLocale();
        $countries = Country::all();
        $countryItems = $countries
            ->map(fn($c) => new ListItemData($c->id, $language == 'ar' ? $c->name_ar : $c->name))
            ->sort(fn($a, $b) => $a->text <=> $b->text);

        return view('auth.complete-profile', compact('countries', 'countryItems'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'phone'      => ['required', 'string', 'max:50'],
        ]);

        $request->user()->update($validated);

        return redirect()->route('dashboard');
    }
}
