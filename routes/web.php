<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Kegiatan\Index as KegiatanIndex;


Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('/kegiatan', KegiatanIndex::class)
        ->name('kegiatan.index');
});

require __DIR__.'/settings.php';