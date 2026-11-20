<?php

use App\Livewire\Account\ChangePassword;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Mahasiswa\DaftarTiket;
use App\Livewire\Mahasiswa\DetailTiket;
use App\Livewire\Mahasiswa\PendaftaranAnggota as MahasiswaPendaftaranAnggota;
use App\Livewire\Pengurus\PresensiKegiatan;
use App\Livewire\Pengurus\PendaftaranAnggota as PengurusPendaftaranAnggota;
use App\Livewire\Ukm\Catalog as UkmCatalog;
use App\Livewire\Ukm\Index as UkmIndex;
use App\Livewire\Ukm\Profile as UkmProfile;
use App\Livewire\Ukm\Team as UkmTeam;
use App\Livewire\Users\Index as UserIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'account.active'])->get('account/password/change', ChangePassword::class)->name('account.password.change');

Route::middleware(['auth', 'account.active', 'password.ready'])->group(function () {
    Route::get('dashboard', DashboardIndex::class)->name('dashboard');
    Route::get('katalog-ukm', UkmCatalog::class)->name('ukm.catalog');
    Route::get('pendaftaran-anggota', MahasiswaPendaftaranAnggota::class)->name('pendaftaran-anggota.create');
    Route::get('pengurus/pengajuan-anggota', PengurusPendaftaranAnggota::class)->name('pengurus.pengajuan-anggota');
    Route::get('ukm', UkmIndex::class)->middleware('can:viewAny,App\\Models\\Ukm')->name('ukm.index');
    Route::get('ukm/profil', UkmProfile::class)->name('ukm.profile');
    Route::get('ukm/tim', UkmTeam::class)->name('ukm.team');
    Route::get('users', UserIndex::class)->name('users.index');

    // Tiket mahasiswa
    Route::get('/tiket-saya', DaftarTiket::class)->name('tiket.index');
    Route::get('/tiket-saya/{kode_tiket}', DetailTiket::class)->name('tiket.show');

    // Validasi dan rekap presensi Pengurus UKM
    Route::get('/pengurus/kegiatan/{kegiatan}/presensi', PresensiKegiatan::class)
        ->name('pengurus.kegiatan.presensi');
});

require __DIR__.'/settings.php';
