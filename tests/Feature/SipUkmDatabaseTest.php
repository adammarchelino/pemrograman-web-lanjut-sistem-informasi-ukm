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

    $expectedUkm = [
        'UKM Geniwangi' => 'Wadah bagi mahasiswa untuk menyalurkan dan mengembangkan bakat di bidang seni rupa, suara, musik, tari, teater, dan kebudayaan.',
        'UKM Ikatan Mahasiswa Muslim (IMAM)' => 'Unit kegiatan kerohanian Islam yang berfokus pada pembinaan karakter, dakwah, pendidikan keagamaan, serta keikutsertaan dalam ajang perlombaan Islami tingkat nasional.',
        'UKM Kewirausahaan' => 'Fokus pada pengembangan jiwa bisnis, pelatihan kewirausahaan, dan inkubasi rintisan usaha bagi mahasiswa.',
        'UKM Korps Sukarela (KSR)' => 'Unit yang bergerak di bidang kemanusiaan, kesehatan, penanganan medis darurat, serta pelayanan sosial (bekerjasama dengan Palang Merah Indonesia).',
        'UKM Mahasiswa Penggiat Alam (Mapala)' => 'Wadah bagi mahasiswa pecinta alam yang aktif dalam kegiatan petualangan alam bebas, konservasi lingkungan, mitigasi bencana, dan penjelajahan.',
        'UKM Riset dan Pengabdian Berdampak (RPB)' => 'Fokus pada kegiatan penelitian ilmiah, inovasi teknologi, penulisan karya tulis, serta program pengabdian kepada masyarakat.',
        'UKM Olahraga' => 'Menjadi wadah pembinaan dan penyaluran minat bakat di berbagai cabang olahraga (seperti sepak bola, bulu tangkis, bola voli, basket, pencak silat/taekwondo, dll.).',
        'UKM Racana Prabu Tawang Alun – Sritanjung' => 'Gugus depan gerakan Pramuka di lingkungan kampus Poliwangi untuk putra dan putri.',
        'UKM Resimen Mahasiswa (Menwa)' => 'Satuan kegiatan mahasiswa yang berfokus pada bela negara, kedisiplinan, keamanan kampus, serta wawasan kebangsaan.',
        'UKM LENTERA (Language, Ethnicity, Nusantara, and Transnational Awareness)' => 'Fokus pada pengembangan kapasitas bahasa asing, apresiasi budaya nusantara, serta wawasan internasional.',
    ];

    $seededUkm = Ukm::query()->get()->keyBy('nama');

    expect($seededUkm)->toHaveCount(10);

    foreach ($expectedUkm as $nama => $deskripsi) {
        expect($seededUkm->has($nama))->toBeTrue()
            ->and($seededUkm[$nama]->deskripsi)->toBe($deskripsi)
            ->and($seededUkm[$nama]->status)->toBe('nonaktif')
            ->and($seededUkm[$nama]->status_pendaftaran_anggota)->toBe('ditutup');
    }

    expect(Ukm::query()->where('nama', 'Pecinta Alam Poliwangi')->exists())->toBeFalse()
        ->and(User::query()->where('email', 'mahasiswa@example.test')->count())->toBe(1)
        ->and(User::query()->where('email', 'test@example.com')->count())->toBe(1)
        ->and(User::query()->where('email', 'pengurus.alam@example.test')->count())->toBe(1)
        ->and(PendaftaranAnggota::query()->count())->toBe(1)
        ->and(Keanggotaan::query()->count())->toBe(1)
        ->and(Kegiatan::query()->where('nama', 'Pelatihan Dasar Pecinta Alam')->count())->toBe(1);
});
