<?php

use App\Livewire\Mahasiswa\DaftarTiket;
use App\Livewire\Mahasiswa\DetailTiket;
use App\Livewire\Pengurus\PresensiKegiatan;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Rute Tiket Mahasiswa
    Route::get('/tiket-saya', DaftarTiket::class)->name('tiket.index');
    Route::get('/tiket-saya/{kode_tiket}', DetailTiket::class)->name('tiket.show');

    // Rute Validasi & Rekap Presensi Pengurus UKM
    Route::get('/pengurus/kegiatan/{kegiatan}/presensi', PresensiKegiatan::class)
        ->name('pengurus.kegiatan.presensi');
});

require __DIR__.'/settings.php';