<?php

use App\Livewire\Kemahasiswaan\PengajuanAnggota;
use App\Livewire\Ukm\Index as UkmIndex;
use App\Models\Keanggotaan;
use App\Models\PendaftaranAnggota;
use App\Models\Ukm;
use App\Models\User;
use Livewire\Livewire;

it('menampilkan pengajuan dari seluruh ukm untuk kemahasiswaan', function () {
    $admin = User::factory()->create(['role' => User::ROLE_KEMAHASISWAAN]);

    $ukmA = Ukm::factory()->create(['nama' => 'UKM Alpha']);
    $ukmB = Ukm::factory()->create(['nama' => 'UKM Beta']);

    PendaftaranAnggota::factory()->create([
        'user_id' => User::factory()->create(['name' => 'Pemohon Alpha'])->id,
        'ukm_id' => $ukmA->id,
        'status' => 'pending',
    ]);

    PendaftaranAnggota::factory()->create([
        'user_id' => User::factory()->create(['name' => 'Pemohon Beta'])->id,
        'ukm_id' => $ukmB->id,
        'status' => 'diterima',
    ]);

    Livewire::actingAs($admin)
        ->test(PengajuanAnggota::class)
        ->assertSee('Pemohon Alpha')
        ->assertSee('Pemohon Beta')
        ->assertSee('UKM Alpha')
        ->assertSee('UKM Beta')
        ->assertViewHas('statistik', fn (array $statistik) => $statistik['semua'] === 2
            && $statistik['pending'] === 1
            && $statistik['diterima'] === 1
            && $statistik['ditolak'] === 0);
});

it('membatasi pemantauan pengajuan hanya untuk kemahasiswaan', function () {
    $admin = User::factory()->create(['role' => User::ROLE_KEMAHASISWAAN]);
    $pengurus = User::factory()->create([
        'role' => User::ROLE_PENGURUS_UKM,
        'ukm_id' => Ukm::factory()->create()->id,
    ]);
    $mahasiswa = User::factory()->create(['role' => User::ROLE_MAHASISWA]);

    $this->actingAs($admin)->get(route('kemahasiswaan.pengajuan-anggota'))->assertOk();
    $this->actingAs($pengurus)->get(route('kemahasiswaan.pengajuan-anggota'))->assertForbidden();
    $this->actingAs($mahasiswa)->get(route('kemahasiswaan.pengajuan-anggota'))->assertForbidden();

    Livewire::actingAs($pengurus)->test(PengajuanAnggota::class)->assertForbidden();
});

it('memfilter pengajuan berdasarkan pencarian, ukm, dan status', function () {
    $admin = User::factory()->create(['role' => User::ROLE_KEMAHASISWAAN]);

    $ukmA = Ukm::factory()->create(['nama' => 'UKM Alpha']);
    $ukmB = Ukm::factory()->create(['nama' => 'UKM Beta']);

    PendaftaranAnggota::factory()->create([
        'user_id' => User::factory()->create(['name' => 'Pemohon Alpha'])->id,
        'ukm_id' => $ukmA->id,
        'status' => 'pending',
    ]);

    PendaftaranAnggota::factory()->create([
        'user_id' => User::factory()->create(['name' => 'Pemohon Beta'])->id,
        'ukm_id' => $ukmB->id,
        'status' => 'diterima',
    ]);

    Livewire::actingAs($admin)
        ->test(PengajuanAnggota::class)
        ->set('search', 'Beta')
        ->assertSee('Pemohon Beta')
        ->assertDontSee('Pemohon Alpha')
        ->call('resetFilters')
        ->set('filterUkm', (string) $ukmA->id)
        ->assertSee('Pemohon Alpha')
        ->assertDontSee('Pemohon Beta')
        ->call('setFilterStatus', 'diterima')
        ->assertDontSee('Pemohon Alpha')
        ->assertSee('Tidak ada pengajuan');
});

it('menghitung anggota aktif dari data keanggotaan pada rekap', function () {
    $admin = User::factory()->create(['role' => User::ROLE_KEMAHASISWAAN]);
    $ukm = Ukm::factory()->create(['nama' => 'UKM Rekap']);

    $buatAnggota = function (string $status) use ($ukm) {
        $pendaftaran = PendaftaranAnggota::factory()->create([
            'ukm_id' => $ukm->id,
            'status' => 'diterima',
        ]);

        Keanggotaan::factory()->create([
            'pendaftaran_anggota_id' => $pendaftaran->id,
            'status' => $status,
        ]);
    };

    $buatAnggota('aktif');
    $buatAnggota('aktif');
    $buatAnggota('nonaktif');

    Livewire::actingAs($admin)
        ->test(PengajuanAnggota::class)
        ->assertViewHas('rekap', function ($rekap) use ($ukm) {
            $baris = $rekap->firstWhere('id', $ukm->id);

            return $baris->anggota_aktif_count === 2
                && $baris->pengajuan_count === 3
                && $baris->pengajuan_diterima_count === 3
                && $baris->pengajuan_pending_count === 0
                && $baris->pengajuan_ditolak_count === 0;
        });
});

it('menampilkan jumlah anggota aktif pada detail ukm', function () {
    $admin = User::factory()->create(['role' => User::ROLE_KEMAHASISWAAN]);
    $ukm = Ukm::factory()->create();

    $buatAnggota = function (string $status) use ($ukm) {
        $pendaftaran = PendaftaranAnggota::factory()->create([
            'ukm_id' => $ukm->id,
            'status' => 'diterima',
        ]);

        Keanggotaan::factory()->create([
            'pendaftaran_anggota_id' => $pendaftaran->id,
            'status' => $status,
        ]);
    };

    $buatAnggota('aktif');
    $buatAnggota('aktif');
    $buatAnggota('nonaktif');

    Livewire::actingAs($admin)
        ->test(UkmIndex::class)
        ->call('openDetailModal', $ukm->id)
        ->assertViewHas('selectedUkm', fn (?Ukm $selected) => $selected?->id === $ukm->id
            && $selected->anggota_aktif_count === 2);
});
