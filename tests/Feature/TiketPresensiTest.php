<?php

use App\Livewire\Pengurus\PresensiKegiatan;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\RegistrasiKegiatan;
use App\Models\TiketKegiatan;
use App\Models\Ukm;
use App\Models\User;
use App\Services\TiketPresensiService;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

test('penerbitan tiket bersifat idempoten dan menghasilkan format TKT dengan 32 karakter heksadesimal', function () {
    $ukm = Ukm::factory()->create();
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $kegiatan = Kegiatan::factory()->create(['ukm_id' => $ukm->id]);

    // Memakai factory() agar kolom wajib (jenis_peserta, nama_peserta, email_peserta, terdaftar_pada) otomatis terisi
    $registrasi = RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'user_id' => $mahasiswa->id,
        'status' => 'terdaftar',
    ]);

    $service = new TiketPresensiService;
    $tiketPertama = $service->terbitkanTiket($registrasi);
    $tiketKedua = $service->terbitkanTiket($registrasi);

    expect($tiketPertama->id)->toBe($tiketKedua->id)
        ->and($tiketPertama->kode_tiket)->toMatch('/^TKT-[0-9A-F]{32}$/')
        ->and(TiketKegiatan::count())->toBe(1);
});

test('halaman tiket memakai navigasi mahasiswa dan menampilkan tautan tiket', function () {
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

    $this->actingAs($mahasiswa)
        ->get(route('tiket.index'))
        ->assertOk()
        ->assertSee('Tiket Saya')
        ->assertSee('Tiket Kegiatan Saya');
});

test('check-in berhasil oleh pengurus UKM yang benar dan mencatat kolom presensi dengan tepat', function () {
    $ukm = Ukm::factory()->create();
    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id]);
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $kegiatan = Kegiatan::factory()->create(['ukm_id' => $ukm->id]);

    $registrasi = RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'user_id' => $mahasiswa->id,
        'status' => 'terdaftar',
    ]);

    $service = new TiketPresensiService;
    $tiket = $service->terbitkanTiket($registrasi);

    $presensi = $service->validasiDanCatatPresensi($tiket->kode_tiket, $kegiatan, $pengurus);

    expect(Presensi::count())->toBe(1)
        ->and($presensi->registrasi_kegiatan_id)->toBe($registrasi->id)
        ->and($presensi->dicatat_oleh)->toBe($pengurus->id)
        ->and($presensi->hadir_pada)->not->toBeNull();
});

test('pengurus dapat menerbitkan tiket untuk peserta terdaftar pada kegiatannya', function () {
    $ukm = Ukm::factory()->create();
    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id]);
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $kegiatan = Kegiatan::factory()->create(['ukm_id' => $ukm->id]);
    $registrasi = RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'user_id' => $mahasiswa->id,
        'status' => 'terdaftar',
    ]);

    Livewire::actingAs($pengurus)
        ->test(PresensiKegiatan::class, ['kegiatan' => $kegiatan])
        ->call('terbitkanTiket', $registrasi->id)
        ->assertHasNoErrors();

    expect(TiketKegiatan::where('registrasi_kegiatan_id', $registrasi->id)->exists())->toBeTrue();
});

test('check-in kedua pada tiket yang sama ditolak dengan ValidationException dan jumlah baris tetap satu', function () {
    $ukm = Ukm::factory()->create();
    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id]);
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $kegiatan = Kegiatan::factory()->create(['ukm_id' => $ukm->id]);

    $registrasi = RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'user_id' => $mahasiswa->id,
        'status' => 'terdaftar',
    ]);

    $service = new TiketPresensiService;
    $tiket = $service->terbitkanTiket($registrasi);

    $service->validasiDanCatatPresensi($tiket->kode_tiket, $kegiatan, $pengurus);

    expect(fn () => $service->validasiDanCatatPresensi($tiket->kode_tiket, $kegiatan, $pengurus))
        ->toThrow(ValidationException::class);

    expect(Presensi::count())->toBe(1);
});

test('tiket berstatus dibatalkan ditolak tanpa membuat record presensi', function () {
    $ukm = Ukm::factory()->create();
    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id]);
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $kegiatan = Kegiatan::factory()->create(['ukm_id' => $ukm->id]);

    $registrasi = RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'user_id' => $mahasiswa->id,
        'status' => 'terdaftar',
    ]);

    $service = new TiketPresensiService;
    $tiket = $service->terbitkanTiket($registrasi);
    $tiket->update(['status' => 'dibatalkan']);

    expect(fn () => $service->validasiDanCatatPresensi($tiket->kode_tiket, $kegiatan, $pengurus))
        ->toThrow(ValidationException::class);

    expect(Presensi::count())->toBe(0);
});

test('tiket dari registrasi yang dibatalkan tidak dapat digunakan untuk check-in', function () {
    $ukm = Ukm::factory()->create();
    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id]);
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $kegiatan = Kegiatan::factory()->create(['ukm_id' => $ukm->id]);
    $registrasi = RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'user_id' => $mahasiswa->id,
        'status' => 'terdaftar',
    ]);

    $service = new TiketPresensiService;
    $tiket = $service->terbitkanTiket($registrasi);
    $registrasi->update(['status' => 'dibatalkan']);

    expect(fn () => $service->validasiDanCatatPresensi($tiket->kode_tiket, $kegiatan, $pengurus))
        ->toThrow(ValidationException::class);

    expect(Presensi::count())->toBe(0);
});

test('pengurus UKM lain menerima 403 di halaman presensi dan ditolak ValidationException pada service', function () {
    $ukmA = Ukm::factory()->create();
    $ukmB = Ukm::factory()->create();

    $pengurusUkmA = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukmA->id]);
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $kegiatanUkmB = Kegiatan::factory()->create(['ukm_id' => $ukmB->id]);

    $registrasi = RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatanUkmB->id,
        'user_id' => $mahasiswa->id,
        'status' => 'terdaftar',
    ]);

    $service = new TiketPresensiService;
    $tiketUkmB = $service->terbitkanTiket($registrasi);

    // 1. Akses via halaman web / Livewire -> ditolak Gate dengan HTTP 403
    $this->actingAs($pengurusUkmA)
        ->get(route('pengurus.kegiatan.presensi', $kegiatanUkmB))
        ->assertForbidden();

    Livewire::actingAs($pengurusUkmA)
        ->test(PresensiKegiatan::class, ['kegiatan' => $kegiatanUkmB])
        ->assertForbidden();

    // 2. Akses langsung ke fungsi Service -> ditolak dengan ValidationException
    expect(fn () => $service->validasiDanCatatPresensi($tiketUkmB->kode_tiket, $kegiatanUkmB, $pengurusUkmA))
        ->toThrow(ValidationException::class);

    expect(Presensi::count())->toBe(0);
});
