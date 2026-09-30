<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShowDashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        return view('dashboard', [
            'activeCount' => File::count(),
            'archivedCount' => 0,
            'lastUpdated' => File::max('created_at'),
            'recent' => File::latest()->limit(10)->get(),
        ]);
    }
}
