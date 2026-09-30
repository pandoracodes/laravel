<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Guest Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProgramController as GuestProgramController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\GaleriController as GuestGaleriController; // Pastikan file ini ada di app/Http/Controllers/
use App\Http\Controllers\ArtikelController as GuestArtikelController; // Pastikan file ini ada di app/Http/Controllers/

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PesertaController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Admin\ArtikelController as AdminArtikelController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;

// --- PUBLIK ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang-kami', fn() => Inertia::render('About'))->name('about');
Route::get('/hubungi-kami', function () {return Inertia::render('Contact'); })->name('contact');
Route::get('/galeri/{galeri}', [GuestGaleriController::class, 'show'])->name('galeri.show');
Route::get('/galeri', [GuestGaleriController::class, 'index'])->name('galeri.index');
Route::get('/artikel', [GuestArtikelController::class, 'index'])->name('artikel.index');
Route::get('/artikel/{artikel:slug}', [GuestArtikelController::class, 'show'])->name('artikel.show');

// Program Kursus (Pakai Prefix Sendiri)
Route::prefix('program-kursus')->name('programs.')->group(function () {
    Route::get('/', [GuestProgramController::class, 'index'])->name('index');
    Route::get('/{program:slug}', [GuestProgramController::class, 'show'])->name('show');
});

// Pendaftaran
Route::get('/daftar', [PendaftaranController::class, 'create'])->name('pendaftaran.umum');
Route::post('/daftar/simpan', [PendaftaranController::class, 'store'])->name('pendaftaran.simpan');


// --- ADMIN (Auth & Verified) ---
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/peserta/export/excel', [PesertaController::class, 'exportExcel'])->name('peserta.export');
 Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    // CRUD Utama
    Route::resource('peserta', PesertaController::class);
    Route::resource('program', AdminProgramController::class);
    Route::resource('artikel', AdminArtikelController::class); // Pakai alias AdminArtikelController
    Route::resource('galeri', AdminGaleriController::class);   // Pakai alias AdminGaleriController
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
});

require __DIR__.'/settings.php';
