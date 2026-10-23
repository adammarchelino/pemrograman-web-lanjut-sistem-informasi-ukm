<?php

use App\Models\Keanggotaan;
use App\Models\Kegiatan;
use App\Models\PendaftaranAnggota;
use App\Models\Presensi;
use App\Models\RegistrasiKegiatan;
use App\Models\TiketKegiatan;
use App\Models\Ukm;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;

it('keeps rejected application history while allowing only one pending or accepted application', function () {
    $user = User::factory()->create();
    $ukm = Ukm::factory()->create();

    $rejected = PendaftaranAnggota::factory()->create([
        'user_id' => $user->id,
        'ukm_id' => $ukm->id,
        'status' => 'ditolak',
    ]);

    $pending = PendaftaranAnggota::factory()->create([
        'user_id' => $user->id,
        'ukm_id' => $ukm->id,
        'status' => 'pending',
    ]);

    expect($rejected->fresh()->status)->toBe('ditolak')
        ->and($pending->status)->toBe('pending');

    expect(fn () => PendaftaranAnggota::factory()->create([
        'user_id' => $user->id,
        'ukm_id' => $ukm->id,
        'status' => 'pending',
    ]))->toThrow(QueryException::class);
});

it('creates exactly one membership when a pending application is accepted', function () {
    $ukm = Ukm::factory()->create();
    $student = User::factory()->create();
    $staff = User::factory()->create(['role' => 'pengurus_ukm', 'ukm_id' => $ukm->id]);
    $application = PendaftaranAnggota::factory()->create([
        'user_id' => $student->id,
        'ukm_id' => $ukm->id,
        'status' => 'pending',
    ]);

    $membership = $application->terima($staff);

    expect($application->fresh()->status)->toBe('diterima')
        ->and($application->fresh()->diputuskan_oleh)->toBe($staff->id)
        ->and($membership->user_id)->toBe($student->id)
        ->and($membership->ukm_id)->toBe($ukm->id)
        ->and(Keanggotaan::query()->where('user_id', $student->id)->where('ukm_id', $ukm->id)->count())->toBe(1);

    $laterApplication = PendaftaranAnggota::factory()->create([
        'user_id' => $student->id,
        'ukm_id' => $ukm->id,
        'status' => 'ditolak',
    ]);

    expect(fn () => Keanggotaan::factory()->create([
        'pendaftaran_anggota_id' => $laterApplication->id,
    ]))->toThrow(QueryException::class);
});

it('prevents duplicate active registrations and allows re-registration after cancellation', function () {
    $activity = Kegiatan::factory()->create();
    $student = User::factory()->create();
    $registration = RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $activity->id,
        'user_id' => $student->id,
        'status' => 'terdaftar',
    ]);

    expect(fn () => RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $activity->id,
        'user_id' => $student->id,
        'status' => 'terdaftar',
    ]))->toThrow(QueryException::class);

    $registration->update(['status' => 'dibatalkan']);

    $resubmission = RegistrasiKegiatan::factory()->create([
        'kegiatan_id' => $activity->id,
        'user_id' => $student->id,
        'status' => 'terdaftar',
    ]);

    expect($resubmission->exists)->toBeTrue()
        ->and(RegistrasiKegiatan::query()->where('status', 'dibatalkan')->count())->toBe(1);
});

it('normalizes public attendee email and prevents duplicate active guest registrations', function () {
    $activity = Kegiatan::factory()->create();
    $base = [
        'kegiatan_id' => $activity->id,
        'user_id' => null,
        'jenis_peserta' => 'umum',
        'nama_peserta' => 'Peserta Umum',
        'nim' => null,
        'status' => 'terdaftar',
    ];

    $guest = RegistrasiKegiatan::factory()->pesertaUmum()->create($base + [
        'email_peserta' => '  Contoh@Example.test ',
    ]);

    expect($guest->fresh()->email_peserta)->toBe('contoh@example.test');

    expect(fn () => RegistrasiKegiatan::factory()->pesertaUmum()->create($base + [
        'email_peserta' => 'contoh@example.test',
    ]))->toThrow(QueryException::class);
});

it('enforces unique NIM, one ticket per registration, and one attendance per registration', function () {
    $student = User::factory()->create(['nim' => '2026000099']);
    $registration = RegistrasiKegiatan::factory()->create();

    expect(fn () => User::factory()->create(['nim' => '2026000099']))
        ->toThrow(QueryException::class);

    TiketKegiatan::factory()->create(['registrasi_kegiatan_id' => $registration->id]);
    expect(fn () => TiketKegiatan::factory()->create(['registrasi_kegiatan_id' => $registration->id]))
        ->toThrow(QueryException::class);

    $ticketRegistration = RegistrasiKegiatan::factory()->create();
    expect(fn () => TiketKegiatan::factory()->create(['kode_tiket' => 'KODE-SAMA-UNIK']))
        ->not->toThrow(QueryException::class);
    expect(fn () => TiketKegiatan::factory()->create(['kode_tiket' => 'KODE-SAMA-UNIK']))
        ->toThrow(QueryException::class);

    Presensi::factory()->create(['registrasi_kegiatan_id' => $ticketRegistration->id]);
    expect(fn () => Presensi::factory()->create(['registrasi_kegiatan_id' => $ticketRegistration->id]))
        ->toThrow(QueryException::class);

    expect(fn () => Presensi::factory()->create(['registrasi_kegiatan_id' => 999999]))
        ->toThrow(QueryException::class);

    expect($student->fresh()->nim)->toBe('2026000099');
});

it('keeps the database seeder safe to run more than once', function () {
    Artisan::call('db:seed');
    Artisan::call('db:seed');

    expect(Ukm::query()->where('nama', 'Pecinta Alam Poliwangi')->count())->toBe(1)
        ->and(User::query()->where('email', 'mahasiswa@example.test')->count())->toBe(1)
        ->and(User::query()->where('email', 'test@example.com')->count())->toBe(1)
        ->and(Kegiatan::query()->where('nama', 'Pelatihan Dasar Pecinta Alam')->count())->toBe(1);
});
