<?php

use App\Models\Kegiatan;
use App\Models\RegistrasiKegiatan;
use App\Models\Ukm;
use App\Models\User;
use App\Services\TiketPresensiService;

function tiketUntukMahasiswa(User $mahasiswa, Kegiatan $kegiatan): string
{
    $registrasi = RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'user_id' => $mahasiswa->id,
        'status' => 'terdaftar',
    ]);

    return (new TiketPresensiService)->terbitkanTiket($registrasi)->kode_tiket;
}

test('mahasiswa dapat membuka daftar dan detail tiket miliknya', function () {
    $ukm = Ukm::factory()->create();
    $mahasiswa = User::factory()->create(['role' => User::ROLE_MAHASISWA]);
    $kegiatan = Kegiatan::factory()->create(['ukm_id' => $ukm->id]);
    $kodeTiket = tiketUntukMahasiswa($mahasiswa, $kegiatan);

    $this->actingAs($mahasiswa)
        ->get(route('tiket.index'))
        ->assertOk()
        ->assertSee('Tiket Acara Saya')
        ->assertSee('Tiket Saya');

    $this->actingAs($mahasiswa)
        ->get(route('tiket.show', $kodeTiket))
        ->assertOk()
        ->assertSee($kodeTiket);
});

test('pengurus UKM menerima 403 pada daftar dan detail tiket peserta', function () {
    $ukm = Ukm::factory()->create();
    $mahasiswa = User::factory()->create(['role' => User::ROLE_MAHASISWA]);
    $pengurus = User::factory()->create([
        'role' => User::ROLE_PENGURUS_UKM,
        'ukm_id' => $ukm->id,
        'jabatan_ukm' => 'ketua',
    ]);
    $kegiatan = Kegiatan::factory()->create(['ukm_id' => $ukm->id]);
    $kodeTiket = tiketUntukMahasiswa($mahasiswa, $kegiatan);

    $this->actingAs($pengurus)
        ->get(route('tiket.index'))
        ->assertForbidden();

    $this->actingAs($pengurus)
        ->get(route('tiket.show', $kodeTiket))
        ->assertForbidden();
});

test('menu tiket tidak tampil untuk pengurus dan navigasi presensi tetap berfungsi', function () {
    $ukm = Ukm::factory()->create();
    $pengurus = User::factory()->create([
        'role' => User::ROLE_PENGURUS_UKM,
        'ukm_id' => $ukm->id,
        'jabatan_ukm' => 'ketua',
    ]);
    $kegiatan = Kegiatan::factory()->create(['ukm_id' => $ukm->id]);

    $this->actingAs($pengurus)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Tiket Saya');

    $this->actingAs($pengurus)
        ->get(route('pengurus.kegiatan.presensi', $kegiatan))
        ->assertOk()
        ->assertSee('Presensi Acara');
});

test('mahasiswa dengan penugasan pengurus UKM mendapat 403 pada daftar dan detail tiket', function () {
    $ukm = Ukm::factory()->create();
    $kegiatan = Kegiatan::factory()->create(['ukm_id' => $ukm->id]);

    $mahasiswaPengurus = User::factory()->create([
        'role' => User::ROLE_MAHASISWA,
        'ukm_id' => $ukm->id,
        'jabatan_ukm' => 'inti',
    ]);

    $kodeTiket = tiketUntukMahasiswa($mahasiswaPengurus, $kegiatan);

    $this->actingAs($mahasiswaPengurus)
        ->get(route('tiket.index'))
        ->assertForbidden();

    $this->actingAs($mahasiswaPengurus)
        ->get(route('tiket.show', $kodeTiket))
        ->assertForbidden();
});