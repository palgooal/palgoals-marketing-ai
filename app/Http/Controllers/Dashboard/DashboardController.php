<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $organization = Organization::query()
            ->with('brandProfile')
            ->first();

        return view('dashboard.index', [
            'organization' => $organization,
        ]);
    }
}
