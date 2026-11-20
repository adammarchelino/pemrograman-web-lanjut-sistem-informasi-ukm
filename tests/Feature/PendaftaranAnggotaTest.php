<?php

use App\Livewire\Mahasiswa\PendaftaranAnggota as FormPendaftaran;
use App\Livewire\Pengurus\PendaftaranAnggota as KelolaPendaftaran;
use App\Models\Keanggotaan;
use App\Models\PendaftaranAnggota;
use App\Models\Ukm;
use App\Models\User;
use Livewire\Livewire;

it('allows a student to submit an application and keeps its user and ukm relations', function () {
    $student = User::factory()->create(['role' => User::ROLE_MAHASISWA]);
    $ukm = Ukm::factory()->create([
        'status' => 'aktif',
        'status_pendaftaran_anggota' => 'dibuka',
        'pendaftaran_anggota_dibuka_pada' => now()->subDay(),
        'pendaftaran_anggota_ditutup_pada' => now()->addDay(),
    ]);

    Livewire::actingAs($student)
        ->test(FormPendaftaran::class)
        ->set('ukmId', $ukm->id)
        ->set('alasan', 'Saya ingin mengembangkan kemampuan dan berkontribusi.')
        ->call('ajukan')
        ->assertHasNoErrors()
        ->assertSee('Pengajuan keanggotaan berhasil dikirim.');

    $application = PendaftaranAnggota::query()->sole();

    expect($application->user_id)->toBe($student->id)
        ->and($application->ukm_id)->toBe($ukm->id)
        ->and($application->status)->toBe('pending')
        ->and($application->user->is($student))->toBeTrue()
        ->and($application->ukm->is($ukm))->toBeTrue()
        ->and(Keanggotaan::query()->count())->toBe(0);
});

it('lets only the assigned ukm manager accept or reject its applications', function () {
    $ukm = Ukm::factory()->create();
    $student = User::factory()->create();
    $manager = User::factory()->create([
        'role' => User::ROLE_PENGURUS_UKM,
        'ukm_id' => $ukm->id,
    ]);
    $otherManager = User::factory()->create([
        'role' => User::ROLE_PENGURUS_UKM,
        'ukm_id' => Ukm::factory()->create()->id,
    ]);

    $application = PendaftaranAnggota::factory()->create([
        'user_id' => $student->id,
        'ukm_id' => $ukm->id,
        'status' => 'pending',
    ]);

    Livewire::actingAs($otherManager)
        ->test(KelolaPendaftaran::class)
        ->call('terima', $application->id)
        ->assertForbidden();

    Livewire::actingAs($manager)
        ->test(KelolaPendaftaran::class)
        ->call('terima', $application->id)
        ->assertHasNoErrors();

    $application->refresh();
    $membership = Keanggotaan::query()->sole();

    expect($application->status)->toBe('diterima')
        ->and($application->diputuskan_oleh)->toBe($manager->id)
        ->and($membership->user_id)->toBe($student->id)
        ->and($membership->ukm_id)->toBe($ukm->id)
        ->and($membership->pendaftaran_anggota_id)->toBe($application->id)
        ->and($membership->status)->toBe('aktif');

    $rejected = PendaftaranAnggota::factory()->create([
        'user_id' => User::factory()->create()->id,
        'ukm_id' => $ukm->id,
        'status' => 'pending',
    ]);

    Livewire::actingAs($manager)
        ->test(KelolaPendaftaran::class)
        ->set("catatan.{$rejected->id}", 'Belum memenuhi persyaratan.')
        ->call('tolak', $rejected->id)
        ->assertHasNoErrors();

    expect($rejected->fresh()->status)->toBe('ditolak')
        ->and($rejected->fresh()->catatan_pengurus)->toBe('Belum memenuhi persyaratan.')
        ->and(Keanggotaan::query()->count())->toBe(1);
});
