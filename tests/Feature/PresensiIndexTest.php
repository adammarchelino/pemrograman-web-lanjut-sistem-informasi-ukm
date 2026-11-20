<?php

use App\Models\Kegiatan;
use App\Models\Ukm;
use App\Models\User;

test('pengurus UKM membuka halaman presensi dan hanya melihat acara UKM-nya', function () {
    $ukmSendiri = Ukm::factory()->create();
    $ukmLain = Ukm::factory()->create();

    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukmSendiri->id]);

    $acaraSendiri = Kegiatan::factory()->create([
        'ukm_id' => $ukmSendiri->id,
        'nama' => 'Latihan Rutin Sendiri',
    ]);
    $acaraLain = Kegiatan::factory()->create([
        'ukm_id' => $ukmLain->id,
        'nama' => 'Acara Milik UKM Lain',
    ]);

    $this->actingAs($pengurus)
        ->get(route('pengurus.presensi.index'))
        ->assertOk()
        ->assertSee('Presensi Acara')
        ->assertSee($acaraSendiri->nama)
        ->assertDontSee($acaraLain->nama);
});

test('mahasiswa tidak dapat membuka halaman presensi pengurus', function () {
    $ukm = Ukm::factory()->create();
    $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

    Kegiatan::factory()->create(['ukm_id' => $ukm->id, 'nama' => 'Acara Rahasia Pengurus']);

    $this->actingAs($mahasiswa)
        ->get(route('pengurus.presensi.index'))
        ->assertForbidden()
        ->assertDontSee('Acara Rahasia Pengurus');
});

test('tautan Buka presensi menunjuk ke ID acara yang benar', function () {
    $ukm = Ukm::factory()->create();
    $ukmLain = Ukm::factory()->create();

    $pengurus = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id]);

    $acaraPertama = Kegiatan::factory()->create(['ukm_id' => $ukm->id]);
    $acaraKedua = Kegiatan::factory()->create(['ukm_id' => $ukm->id]);
    $acaraLain = Kegiatan::factory()->create(['ukm_id' => $ukmLain->id]);

    $this->actingAs($pengurus)
        ->get(route('pengurus.presensi.index'))
        ->assertOk()
        ->assertSee(route('pengurus.kegiatan.presensi', $acaraPertama), false)
        ->assertSee(route('pengurus.kegiatan.presensi', $acaraKedua), false)
        ->assertDontSee(route('pengurus.kegiatan.presensi', $acaraLain), false);

    expect(route('pengurus.kegiatan.presensi', $acaraPertama))
        ->toContain('/pengurus/kegiatan/'.$acaraPertama->id.'/presensi');
});
