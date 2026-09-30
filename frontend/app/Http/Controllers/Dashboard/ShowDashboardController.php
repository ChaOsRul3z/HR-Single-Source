<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\Models\Document;
=======
use App\Models\File;
use Illuminate\Contracts\View\View;
>>>>>>> c41d43415be9c02915e96f5df0ba22e0adfa31d1
use Illuminate\Http\Request;

class ShowDashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
<<<<<<< HEAD
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
=======
        return view('dashboard', [
            'activeCount' => File::count(),
            'archivedCount' => 0,
            'lastUpdated' => File::max('created_at'),
            'documents' => File::latest()->take(2)->get(),
>>>>>>> c41d43415be9c02915e96f5df0ba22e0adfa31d1
        ]);
    }
}
