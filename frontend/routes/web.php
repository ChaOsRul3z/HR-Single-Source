<?php

use App\Http\Controllers\Dashboard\ShowDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use Illuminate\Http\Request;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', ShowDashboardController::class)->name('dashboard');
    Route::livewire('documents', 'pages::documents.upload')->name('documents.upload');

    Route::get('/admin/documents', [DocumentController::class, 'admin'])->name('admin.documents');
    Route::get('/search', [DocumentController::class, 'search'])->name('search');
});

Route::post('/email/verification-notification', function (Request $request) {
    // Check if user is already verified
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->intended('/dashboard');
    }

    // Resend the notification
    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

require __DIR__.'/settings.php';
