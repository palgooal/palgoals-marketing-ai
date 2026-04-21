<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    private const EDITABLE_KEYS = [
        'app_name',
        'support_email',
        'default_primary_language',
        'default_secondary_language',
    ];

    public function edit(): View
    {
        $storedSettings = Setting::query()
            ->whereIn('key', self::EDITABLE_KEYS)
            ->pluck('value', 'key')
            ->all();

        return view('settings.edit', [
            'settings' => array_replace(array_fill_keys(self::EDITABLE_KEYS, null), $storedSettings),
        ]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }

        return redirect()
            ->route('settings.edit')
            ->with('status', 'Settings updated successfully.');
    }
}
