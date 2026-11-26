<?php

use App\Livewire\Kegiatan\Index as KegiatanIndex;
use App\Models\Kegiatan;
use App\Models\RegistrasiKegiatan;
use App\Models\TiketKegiatan;
use App\Models\Ukm;
use App\Models\User;
use Livewire\Livewire;

/**
 * Kegiatan yang sedang membuka pendaftaran dan sudah dipublikasikan.
 */
function kegiatanUjiTerbuka(Ukm $ukm, array $atribut = []): Kegiatan
{
    return Kegiatan::factory()->create(array_merge([
        'ukm_id' => $ukm->id,
        'status' => 'dipublikasikan',
        'kapasitas' => 5,
        'pendaftaran_dibuka_pada' => now()->subHour(),
        'pendaftaran_ditutup_pada' => now()->addHour(),
        'mulai_pada' => now()->addDay(),
        'selesai_pada' => now()->addDay()->addHour(),
    ], $atribut));
}

test('mahasiswa dapat mendaftar kegiatan yang sedang dibuka', function () {
    $ukm = Ukm::factory()->create();
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'nim' => '1234567890']);
    $kegiatan = kegiatanUjiTerbuka($ukm);

    Livewire::actingAs($mahasiswa)
        ->test(KegiatanIndex::class)
        ->call('daftarKegiatan', $kegiatan->id)
        ->assertHasNoErrors();

    $registrasi = RegistrasiKegiatan::sole();

    expect($registrasi->kegiatan_id)->toBe($kegiatan->id)
        ->and($registrasi->user_id)->toBe($mahasiswa->id)
        ->and($registrasi->status)->toBe('terdaftar')
        ->and($registrasi->nim)->toBe('1234567890')
        ->and($registrasi->jenis_peserta)->toBe('mahasiswa_poliwangi');
});

test('pendaftaran ganda pada kegiatan yang sama ditolak', function () {
    $ukm = Ukm::factory()->create();
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $kegiatan = kegiatanUjiTerbuka($ukm);

    Livewire::actingAs($mahasiswa)
        ->test(KegiatanIndex::class)
        ->call('daftarKegiatan', $kegiatan->id)
        ->call('daftarKegiatan', $kegiatan->id)
        ->assertHasErrors(['pendaftaran' => 'Kamu sudah terdaftar pada kegiatan ini.']);

    expect(RegistrasiKegiatan::count())->toBe(1);
});

test('pendaftaran ditolak ketika kuota kegiatan sudah penuh', function () {
    $ukm = Ukm::factory()->create();
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $kegiatan = kegiatanUjiTerbuka($ukm, ['kapasitas' => 1]);

    RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'status' => 'terdaftar',
    ]);

    Livewire::actingAs($mahasiswa)
        ->test(KegiatanIndex::class)
        ->call('daftarKegiatan', $kegiatan->id)
        ->assertHasErrors(['pendaftaran' => 'Kuota kegiatan sudah penuh.']);

    expect(RegistrasiKegiatan::count())->toBe(1);
});

test('pendaftaran ditolak sebelum jadwal pendaftaran dibuka', function () {
    $ukm = Ukm::factory()->create();
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $kegiatan = kegiatanUjiTerbuka($ukm, [
        'pendaftaran_dibuka_pada' => now()->addHour(),
        'pendaftaran_ditutup_pada' => now()->addHours(2),
    ]);

    Livewire::actingAs($mahasiswa)
        ->test(KegiatanIndex::class)
        ->call('daftarKegiatan', $kegiatan->id)
        ->assertHasErrors(['pendaftaran' => 'Pendaftaran belum dibuka.']);

    expect(RegistrasiKegiatan::count())->toBe(0);
});

test('mahasiswa hanya melihat kegiatan yang sudah dipublikasikan', function () {
    $ukm = Ukm::factory()->create();
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

    $draf = Kegiatan::factory()->create([
        'ukm_id' => $ukm->id,
        'nama' => 'Rapat Internal UKM',
        'status' => 'draf',
    ]);

    $publik = kegiatanUjiTerbuka($ukm, ['nama' => 'Seminar Publik']);

    Livewire::actingAs($mahasiswa)
        ->test(KegiatanIndex::class)
        ->assertDontSee($draf->nama)
        ->assertSee($publik->nama)
        ->assertSet('tampilkanPeserta', false)
        ->assertSet('tampilkanForm', false);
});

test('pengurus UKM lain tidak melihat kegiatan UKM orang lain', function () {
    $ukmSendiri = Ukm::factory()->create();
    $ukmLain = Ukm::factory()->create();
    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukmSendiri->id]);

    $kegiatanSendiri = kegiatanUjiTerbuka($ukmSendiri, ['nama' => 'Kegiatan Sendiri']);
    $kegiatanLain = kegiatanUjiTerbuka($ukmLain, ['nama' => 'Kegiatan UKM Lain']);

    Livewire::actingAs($pengurus)
        ->test(KegiatanIndex::class)
        ->assertSee($kegiatanSendiri->nama)
        ->assertDontSee($kegiatanLain->nama);
});

test('pengurus dapat membuka daftar peserta kegiatan milik UKM-nya', function () {
    $ukm = Ukm::factory()->create();
    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id]);
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Peserta Uji']);
    $kegiatan = kegiatanUjiTerbuka($ukm);

    RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'user_id' => $mahasiswa->id,
        'nama_peserta' => 'Peserta Uji',
        'status' => 'terdaftar',
    ]);

    Livewire::actingAs($pengurus)
        ->test(KegiatanIndex::class)
        ->call('lihatPeserta', $kegiatan->id)
        ->assertSet('tampilkanPeserta', true)
        ->assertSee('Daftar Peserta')
        ->assertSee('Peserta Uji')
        ->call('tutupPeserta')
        ->assertSet('tampilkanPeserta', false);
});

test('mahasiswa dapat membatalkan pendaftarannya sendiri sebelum tiket terbit', function () {
    $ukm = Ukm::factory()->create();
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $kegiatan = kegiatanUjiTerbuka($ukm);

    $registrasi = RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'user_id' => $mahasiswa->id,
        'status' => 'terdaftar',
    ]);

    Livewire::actingAs($mahasiswa)
        ->test(KegiatanIndex::class)
        ->call('batalkanRegistrasi', $registrasi->id)
        ->assertHasNoErrors();

    expect($registrasi->fresh()->status)->toBe('dibatalkan');
});

test('pembatalan ditolak ketika tiket peserta sudah terbit', function () {
    $ukm = Ukm::factory()->create();
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
    $kegiatan = kegiatanUjiTerbuka($ukm);

    $registrasi = RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'user_id' => $mahasiswa->id,
        'status' => 'terdaftar',
    ]);

    TiketKegiatan::create([
        'registrasi_kegiatan_id' => $registrasi->id,
        'kode_tiket' => 'TKT-'.strtoupper(bin2hex(random_bytes(16))),
        'diterbitkan_pada' => now(),
        'status' => 'aktif',
    ]);

    Livewire::actingAs($mahasiswa)
        ->test(KegiatanIndex::class)
        ->call('batalkanRegistrasi', $registrasi->id)
        ->assertHasErrors(['pembatalan' => 'Registrasi tidak bisa dibatalkan karena sudah memiliki tiket atau presensi.']);

    expect($registrasi->fresh()->status)->toBe('terdaftar');
});

test('kegiatan yang sudah memiliki riwayat peserta tidak dapat dihapus', function () {
    $ukm = Ukm::factory()->create();
    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id]);
    $kegiatan = kegiatanUjiTerbuka($ukm);

    RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $kegiatan->id,
        'status' => 'dibatalkan',
    ]);

    Livewire::actingAs($pengurus)
        ->test(KegiatanIndex::class)
        ->call('hapusKegiatan', $kegiatan->id)
        ->assertHasErrors(['hapus']);

    expect(Kegiatan::whereKey($kegiatan->id)->exists())->toBeTrue();
});

test('pengurus dapat menghapus kegiatan yang belum memiliki peserta', function () {
    $ukm = Ukm::factory()->create();
    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id]);
    $kegiatan = kegiatanUjiTerbuka($ukm);

    Livewire::actingAs($pengurus)
        ->test(KegiatanIndex::class)
        ->call('hapusKegiatan', $kegiatan->id)
        ->assertHasNoErrors();

    expect(Kegiatan::whereKey($kegiatan->id)->exists())->toBeFalse();
});
