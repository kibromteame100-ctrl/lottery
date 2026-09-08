<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get();
        $grouped  = $settings->groupBy('group');

        return view('admin.settings.index', compact('grouped'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings'   => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $old = Setting::all()->pluck('value', 'key')->toArray();

        foreach ($data['settings'] as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        AuditLog::record(
            action: 'settings_updated',
            entityType: Setting::class,
            entityId: null,
            oldValues: $old,
            newValues: $data['settings'],
        );

        return back()->with('success', __('settings.updated_successfully'));
    }

    public function setLocale(Request $request)
    {
        $locale = $request->validate([
            'locale' => ['required', 'in:en,am,ti'],
        ])['locale'];

        session(['locale' => $locale]);

        if (auth()->check()) {
            auth()->user()->update(['preferred_locale' => $locale]);
        }

        return back();
    }

    public function setTheme(Request $request)
    {
        $theme = $request->validate([
            'theme' => ['required', 'in:light,dark,system'],
        ])['theme'];

        session(['theme' => $theme]);

        if (auth()->check()) {
            auth()->user()->update(['theme_preference' => $theme]);
        }

        return back();
    }
}
