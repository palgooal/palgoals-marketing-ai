<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\UpdateBrandProfileRequest;
use App\Models\BrandProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BrandProfileController extends Controller
{
    public function edit(): View
    {
        $brandProfile = BrandProfile::query()
            ->with('organization')
            ->firstOrFail();

        return view('brand.edit', [
            'brandProfile' => $brandProfile,
        ]);
    }

    public function update(UpdateBrandProfileRequest $request): RedirectResponse
    {
        $brandProfile = BrandProfile::query()->firstOrFail();

        $brandProfile->update($request->validated());

        return redirect()
            ->route('brand.edit')
            ->with('status', 'Brand profile updated successfully.');
    }
}
