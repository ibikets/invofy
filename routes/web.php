<?php

use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

//Route::get('/', function () {
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});
//
//Route::get('/dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');
//
//Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//    Route::get('/admin/impersonate/{tenantId}', [ImpersonationController::class, 'start'])->name('admin.impersonate.start');
//    Route::get('/admin/impersonate/stop', [ImpersonationController::class, 'stop'])->name('admin.impersonate.stop');
//
//});

Route::get('/', function () {
    return view('welcome'); // your landing page
})->name('landing');

/**
 * Tenant routes: invofy.test/{tenant}/...
 * You can add auth middleware inside this group later.
 */
Route::prefix('{tenant}')
    ->middleware(['tenant']) // resolves CurrentTenant from first URL segment
    ->group(function () {

        Route::get('/dashboard', function () {
            // Example tenant-aware page; replace with a controller later
            $tenant = app(\App\Support\CurrentTenant::class)->tenant();
            return "Tenant dashboard for: " . ($tenant?->name ?? 'Unknown');
        })->name('tenant.dashboard');

        // Add more tenant routes here, e.g. invoices, customers, etc.
    });

Route::middleware(['web'])->group(function () {
    Route::get('/admin/impersonate/{tenantId}', [ImpersonationController::class, 'start'])->name('admin.impersonate.start');
    Route::get('/admin/impersonate/stop', [ImpersonationController::class, 'stop'])->name('admin.impersonate.stop');
});

require __DIR__.'/auth.php';
