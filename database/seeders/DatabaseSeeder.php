<?php

namespace Database\Seeders;

use App\Models\Keanggotaan;
use App\Models\Kegiatan;
use App\Models\PendaftaranAnggota;
use App\Models\RegistrasiKegiatan;
use App\Models\TiketKegiatan;
use App\Models\Ukm;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Keep the starter kit's local test account, but make seeding repeatable.
        User::firstOrCreate(['email' => 'test@example.com'], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);

        $mahasiswa = User::firstOrCreate(['email' => 'mahasiswa@example.test'], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
            'nim' => '2026000001',
            'role' => 'mahasiswa',
        ]);

        $ukm = Ukm::firstOrCreate(['nama' => 'Pecinta Alam Poliwangi'], [
            'deskripsi' => 'UKM kegiatan alam terbuka dan konservasi lingkungan.',
            'visi' => 'Membangun mahasiswa yang peduli alam dan tangguh.',
            'misi' => 'Mengadakan kegiatan pendidikan, konservasi, dan ekspedisi.',
            'email_kontak' => 'alam@example.test',
            'nomor_kontak' => '081234567890',
            'status' => 'aktif',
            'status_pendaftaran_anggota' => 'dibuka',
            'pendaftaran_anggota_dibuka_pada' => now()->subDay(),
            'pendaftaran_anggota_ditutup_pada' => now()->addMonth(),
        ]);

        $pengurus = User::firstOrCreate(['email' => 'pengurus.alam@example.test'], [
            'name' => 'Pengurus UKM Alam',
            'password' => Hash::make('password'),
            'role' => 'pengurus_ukm',
            'ukm_id' => $ukm->id,
        ]);

        $application = PendaftaranAnggota::firstOrCreate(
            ['user_id' => $mahasiswa->id, 'ukm_id' => $ukm->id, 'status' => 'diterima'],
            [
                'jawaban_formulir' => ['alasan' => 'Ingin belajar kegiatan alam terbuka.'],
                'diajukan_pada' => now()->subDays(7),
                'catatan_pengurus' => 'Diterima untuk periode uji.',
                'diputuskan_oleh' => $pengurus->id,
                'diputuskan_pada' => now()->subDays(5),
            ],
        );

        Keanggotaan::firstOrCreate(
            ['user_id' => $mahasiswa->id, 'ukm_id' => $ukm->id],
            [
                'pendaftaran_anggota_id' => $application->id,
                'tanggal_bergabung' => now()->subDays(5)->toDateString(),
                'status' => 'aktif',
            ],
        );

        $kegiatan = Kegiatan::firstOrCreate(
            ['ukm_id' => $ukm->id, 'nama' => 'Pelatihan Dasar Pecinta Alam'],
            [
                'deskripsi' => 'Kegiatan pelatihan dasar untuk calon anggota.',
                'mulai_pada' => now()->addWeeks(2),
                'selesai_pada' => now()->addWeeks(2)->addHours(6),
                'lokasi' => 'Politeknik Negeri Banyuwangi',
                'kapasitas' => 40,
                'audiens' => 'mahasiswa_poliwangi',
                'pendaftaran_dibuka_pada' => now(),
                'pendaftaran_ditutup_pada' => now()->addWeek(),
                'status' => 'draf',
            ],
        );

        $registration = RegistrasiKegiatan::firstOrCreate(
            ['kegiatan_id' => $kegiatan->id, 'user_id' => $mahasiswa->id],
            [
                'jenis_peserta' => 'mahasiswa_poliwangi',
                'nama_peserta' => $mahasiswa->name,
                'email_peserta' => $mahasiswa->email,
                'nim' => $mahasiswa->nim,
                'status' => 'terdaftar',
                'terdaftar_pada' => now(),
            ],
        );

        TiketKegiatan::firstOrCreate(
            ['registrasi_kegiatan_id' => $registration->id],
            ['kode_tiket' => Str::random(48), 'diterbitkan_pada' => now(), 'status' => 'aktif'],
        );

        // Presensi stays absent until the event takes place.
    }
}
