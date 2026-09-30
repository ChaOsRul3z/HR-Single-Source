<?php

use App\Http\Controllers\Dashboard\ShowDashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', ShowDashboardController::class)->name('dashboard');
});

require __DIR__.'/settings.php';
