<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;

class ShowDashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $baseQuery = Document::accessibleBy($user);

        $activeCount = (clone $baseQuery)->active()->count();
        $archivedCount = (clone $baseQuery)->archived()->count();
        $lastUpdated = (clone $baseQuery)->latest('updated_at')->value('updated_at');
        $recent = (clone $baseQuery)->latest('updated_at')->take(5)->get();

        return view('dashboard', [
            'activeCount' => $activeCount,
            'archivedCount' => $archivedCount,
            'lastUpdated' => $lastUpdated,
            'recent' => $recent,
        ]);
    }
}
