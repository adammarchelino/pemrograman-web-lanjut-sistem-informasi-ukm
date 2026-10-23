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
use LogicException;

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

        $dataUkm = [
            [
                'nama' => 'UKM Geniwangi',
                'deskripsi' => 'Wadah bagi mahasiswa untuk menyalurkan dan mengembangkan bakat di bidang seni rupa, suara, musik, tari, teater, dan kebudayaan.',
            ],
            [
                'nama' => 'UKM Ikatan Mahasiswa Muslim (IMAM)',
                'deskripsi' => 'Unit kegiatan kerohanian Islam yang berfokus pada pembinaan karakter, dakwah, pendidikan keagamaan, serta keikutsertaan dalam ajang perlombaan Islami tingkat nasional.',
            ],
            [
                'nama' => 'UKM Kewirausahaan',
                'deskripsi' => 'Fokus pada pengembangan jiwa bisnis, pelatihan kewirausahaan, dan inkubasi rintisan usaha bagi mahasiswa.',
            ],
            [
                'nama' => 'UKM Korps Sukarela (KSR)',
                'deskripsi' => 'Unit yang bergerak di bidang kemanusiaan, kesehatan, penanganan medis darurat, serta pelayanan sosial (bekerjasama dengan Palang Merah Indonesia).',
            ],
            [
                'nama' => 'UKM Mahasiswa Penggiat Alam (Mapala)',
                'deskripsi' => 'Wadah bagi mahasiswa pecinta alam yang aktif dalam kegiatan petualangan alam bebas, konservasi lingkungan, mitigasi bencana, dan penjelajahan.',
            ],
            [
                'nama' => 'UKM Riset dan Pengabdian Berdampak (RPB)',
                'deskripsi' => 'Fokus pada kegiatan penelitian ilmiah, inovasi teknologi, penulisan karya tulis, serta program pengabdian kepada masyarakat.',
            ],
            [
                'nama' => 'UKM Olahraga',
                'deskripsi' => 'Menjadi wadah pembinaan dan penyaluran minat bakat di berbagai cabang olahraga (seperti sepak bola, bulu tangkis, bola voli, basket, pencak silat/taekwondo, dll.).',
            ],
            [
                'nama' => 'UKM Racana Prabu Tawang Alun – Sritanjung',
                'deskripsi' => 'Gugus depan gerakan Pramuka di lingkungan kampus Poliwangi untuk putra dan putri.',
            ],
            [
                'nama' => 'UKM Resimen Mahasiswa (Menwa)',
                'deskripsi' => 'Satuan kegiatan mahasiswa yang berfokus pada bela negara, kedisiplinan, keamanan kampus, serta wawasan kebangsaan.',
            ],
            [
                'nama' => 'UKM LENTERA (Language, Ethnicity, Nusantara, and Transnational Awareness)',
                'deskripsi' => 'Fokus pada pengembangan kapasitas bahasa asing, apresiasi budaya nusantara, serta wawasan internasional.',
            ],
        ];

        $mapalaName = 'UKM Mahasiswa Penggiat Alam (Mapala)';
        $legacyMapala = Ukm::query()->where('nama', 'Pecinta Alam Poliwangi')->first();
        $mapala = Ukm::query()->where('nama', $mapalaName)->first();

        if ($legacyMapala && $mapala && $legacyMapala->isNot($mapala)) {
            throw new LogicException('Data UKM Mapala lama dan baru sama-sama ada; periksa relasi sebelum menjalankan seeder.');
        }

        $ukmByName = [];

        foreach ($dataUkm as $data) {
            $ukm = Ukm::query()->firstOrNew(['nama' => $data['nama']]);

            if ($data['nama'] === $mapalaName && ! $ukm->exists && $legacyMapala) {
                $ukm = $legacyMapala;
            }

            $ukm->fill([
                'nama' => $data['nama'],
                'deskripsi' => $data['deskripsi'],
                'status' => 'nonaktif',
                'status_pendaftaran_anggota' => 'ditutup',
                'pendaftaran_anggota_dibuka_pada' => null,
                'pendaftaran_anggota_ditutup_pada' => null,
            ]);

            if ($data['nama'] === $mapalaName && $legacyMapala) {
                // Clear only the old seeder's known fictitious profile details.
                if ($ukm->visi === 'Membangun mahasiswa yang peduli alam dan tangguh.') {
                    $ukm->visi = null;
                }
                if ($ukm->misi === 'Mengadakan kegiatan pendidikan, konservasi, dan ekspedisi.') {
                    $ukm->misi = null;
                }
                if ($ukm->email_kontak === 'alam@example.test') {
                    $ukm->email_kontak = null;
                }
                if ($ukm->nomor_kontak === '081234567890') {
                    $ukm->nomor_kontak = null;
                }
            }

            $ukm->save();
            $ukmByName[$data['nama']] = $ukm;
        }

        $ukm = $ukmByName[$mapalaName];

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
