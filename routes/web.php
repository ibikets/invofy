<?php

use App\Http\Controllers\DashboardRedirectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tenant\CustomersController;
use App\Http\Controllers\Tenant\VendorsController;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/**
 * Global patterns
 * - Restrict what a {tenant} can be (letters, numbers, hyphen)
 */
Route::pattern('tenant', '[A-Za-z0-9\-]+');

/**
 * Landing (no tenant)
 */
Route::get('/', function () {
    return view('welcome');
})->name('landing');

/**
 * Breeze "dashboard" without tenant:
 * After login, send the user to /{tenant}/dashboard based on their tenant_id.
 */
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardRedirectController::class, 'toTenant'])->name('dashboard.redirect');
    Route::get('/settings', [DashboardRedirectController::class, 'toTenantSettings'])->name('settings.redirect');
});

/**
 * Tenant routes: invofy.test/{tenant}/...
 * Order matters: keep this AFTER the non-tenant /dashboard and /settings routes
 */
Route::prefix('{tenant}')
    ->middleware(['tenant', 'permissions.team', 'auth']) // tenant resolution -> set team -> require login
    ->group(function () {
        Route::get('/dashboard', function () {
            $tenant = app(CurrentTenant::class)->tenant();
            return "Tenant dashboard for: " . ($tenant?->name ?? 'Unknown');
        })->name('tenant.dashboard');

        // Directory — protect with role if you want (Owner/Admin/Finance)
        Route::middleware(['role:Owner|Admin|Finance'])->group(function () {
            // Customers
            Route::get('/customers', [CustomersController::class, 'index'])->name('customers.index');
            Route::post('/customers', [CustomersController::class, 'store'])->name('customers.store');
            Route::get('/customers/{customer}', [CustomersController::class, 'show'])->name('customers.show');
            Route::match(['put','patch'], '/customers/{customer}', [CustomersController::class, 'update'])->name('customers.update');
            Route::delete('/customers/{customer}', [CustomersController::class, 'destroy'])->name('customers.destroy');

            // Vendors
            Route::get('/vendors', [VendorsController::class, 'index'])->name('vendors.index');
            Route::post('/vendors', [VendorsController::class, 'store'])->name('vendors.store');
            Route::get('/vendors/{vendor}', [VendorsController::class, 'show'])->name('vendors.show');
            Route::match(['put','patch'], '/vendors/{vendor}', [VendorsController::class, 'update'])->name('vendors.update');
            Route::delete('/vendors/{vendor}', [VendorsController::class, 'destroy'])->name('vendors.destroy');
        });

        // Example tenant-only page (Owner|Admin required). Adjust roles as needed.
        Route::get('/settings', function () {
            return 'Tenant Settings';
        })->middleware('role:Owner|Admin')->name('tenant.settings');
    });

/**
 * Auth routes (Breeze) are already registered by Breeze's install.
 * If not, ensure auth scaffold is installed.
 */


//Route::get('/', function () {
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});

//Route::get('/dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
