<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SanctionController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResidentProfileController;
use App\Http\Controllers\SecurityController;
use Illuminate\Support\Facades\Route;

Route::get('/debug', function () {
    return [
        'secure' => request()->isSecure(),
        'scheme' => request()->getScheme(),
        'url' => url('/'),
        'asset' => asset('build/assets/app-DumlMiDH.css'),
        'headers' => request()->headers->all(),
    ];
});

// 1. SMART ROOT ROUTE
Route::get('/', function () {
    // 1. Cek User Admin/Security (Auth Bawaan Laravel)
    if (auth()->check()) {
        // Gunakan helper di Model User untuk redirect (Dashboard / Scan)
        $target = auth()->user()->getRedirectRoute();

        return to_route($target);
    }

    // 2. Cek User Resident (Session Custom)
    if (session()->has('resident_unit_id')) {
        return to_route('booking.home');
    }

    return to_route('login');
})->name('home');

// =========================================================
// 2. AREA PENGHUNI
// =========================================================
// SEKARANG SUDAH PAKAI STRING 'resident' (TIDAK ERROR LAGI)
Route::middleware(['resident'])->group(function () {

    Route::get('/booking', [BookingController::class, 'index'])->name('booking.home');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/ticket/{booking:booking_code}', [BookingController::class, 'showTicket'])->name('booking.ticket');
    Route::get('/ticket/{booking:booking_code}/pdf', [BookingController::class, 'downloadPdf'])->name('booking.pdf');
    Route::get('/my-tickets', [BookingController::class, 'myTickets'])->name('booking.list');
    Route::post('/my-tickets', [BookingController::class, 'checkTickets'])->name('booking.check');
    Route::get('/info', [ResidentProfileController::class, 'index'])->name('booking.info');
    Route::put('/update-pin', [ResidentProfileController::class, 'updatePin'])->name('resident.update-pin');

});

// 3. AREA SECURITY
Route::middleware(['auth', 'verified', 'role:security'])
    ->prefix('security')
    ->name('security.')
    ->group(function () {
        Route::get('/scan', [SecurityController::class, 'index'])->name('scan');
        Route::post('/validate-ticket', [SecurityController::class, 'validateTicket'])->name('validate');
        Route::post('/check-in', [SecurityController::class, 'checkIn'])->name('checkin');
    });

// GROUP ADMIN (Hanya bisa diakses User dengan role 'admin')
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/booking', [DashboardController::class, 'store'])->name('dashboard.store');
        Route::post('/dashboard/booking/{booking}/cancel', [DashboardController::class, 'cancel'])
            ->name('booking.cancel');
        Route::resource('users', UserController::class);

        // 2. Unit Management (CRUD)
        Route::resource('units', UnitController::class);
        Route::post('/units/import', [UnitController::class, 'import'])->name('units.import');
        Route::post('/units/{unit}/reset-pin', [UnitController::class, 'resetPin'])->name('units.reset-pin');
        Route::post('/units/{unit}/update-pin', [UnitController::class, 'updatePin'])->name('units.update-pin');
        Route::get('/sanctions', [SanctionController::class, 'index'])->name('sanctions.index');
        Route::delete('/sanctions/{unit}', [SanctionController::class, 'destroy'])->name('sanctions.destroy');
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');

        // --- TAMBAHKAN 2 BARIS INI AGAR TIDAK 404 ---
        Route::put('/maintenance/{id}', [MaintenanceController::class, 'update'])->name('maintenance.update');
        Route::delete('/maintenance/{id}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    });

// 5. PROFIL
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 6. AUTH
require __DIR__.'/auth.php';
