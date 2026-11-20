<?php

use App\Livewire\Account\ChangePassword;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Kegiatan\Index as KegiatanIndex;
use App\Livewire\Mahasiswa\DaftarTiket;
use App\Livewire\Mahasiswa\DetailTiket;
use App\Livewire\Pengurus\PresensiIndex;
use App\Livewire\Pengurus\PresensiKegiatan;
use App\Livewire\Ukm\Catalog as UkmCatalog;
use App\Livewire\Ukm\Index as UkmIndex;
use App\Livewire\Ukm\Profile as UkmProfile;
use App\Livewire\Ukm\Team as UkmTeam;
use App\Livewire\Users\Index as UserIndex;
use App\Models\Ukm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $ukmAktif = Ukm::query()
        ->where('status', Ukm::STATUS_AKTIF)
        ->orderBy('nama')
        ->get(['id', 'nama', 'logo', 'deskripsi', 'status_pendaftaran_anggota']);

    return view('welcome', [
        'ukm' => $ukmAktif,
        'totalUkm' => Ukm::query()->count(),
        'totalAktif' => $ukmAktif->count(),
        'pendaftaranDibuka' => $ukmAktif->where('status_pendaftaran_anggota', Ukm::OPREC_DIBUKA)->count(),
    ]);
})->name('home');

// Ganti password akun
Route::middleware(['auth', 'account.active'])
    ->get('account/password/change', ChangePassword::class)
    ->name('account.password.change');

// Halaman yang memerlukan autentikasi
Route::middleware([
    'auth',
    'account.active',
    'password.ready',
])->group(function () {

    // Dashboard
    Route::get('dashboard', DashboardIndex::class)
        ->name('dashboard');

    // Katalog UKM
    Route::get('katalog-ukm', UkmCatalog::class)
        ->name('ukm.catalog');

    // Pengelolaan UKM
    Route::get('ukm', UkmIndex::class)
        ->middleware('can:viewAny,App\Models\Ukm')
        ->name('ukm.index');

    Route::get('ukm/profil', UkmProfile::class)
        ->name('ukm.profile');

    Route::get('ukm/tim', UkmTeam::class)
        ->name('ukm.team');

    // Pengelolaan pengguna
    Route::get('users', UserIndex::class)
        ->name('users.index');

    // Manajemen Kegiatan UKM
    Route::get('/kegiatan', KegiatanIndex::class)
        ->name('kegiatan.index');

    // Tiket mahasiswa
    Route::get('/tiket-saya', DaftarTiket::class)
        ->name('tiket.index');

    Route::get('/tiket-saya/{kode_tiket}', DetailTiket::class)
        ->name('tiket.show');

    // Daftar acara untuk presensi pengurus UKM
    Route::get('/pengurus/presensi', PresensiIndex::class)
        ->name('pengurus.presensi.index');

    // Validasi dan rekap presensi pengurus UKM
    Route::get(
        '/pengurus/kegiatan/{kegiatan}/presensi',
        PresensiKegiatan::class
    )->name('pengurus.kegiatan.presensi');

});

require __DIR__.'/settings.php';
