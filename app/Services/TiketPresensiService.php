<?php

namespace App\Services;

use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\RegistrasiKegiatan;
use App\Models\TiketKegiatan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TiketPresensiService
{
    /**
     * Membangkitkan kode tiket acak (opaque token >= 128 bit) yang unik.
     */
    public function buatKodeTiketUnik(): string
    {
        do {
            $kode = 'TKT-'.strtoupper(bin2hex(random_bytes(16)));
        } while (TiketKegiatan::where('kode_tiket', $kode)->exists());

        return $kode;
    }

    /**
     * Menerbitkan tiket dari registrasi kegiatan yang berstatus terdaftar (idempoten).
     */
    public function terbitkanTiket(RegistrasiKegiatan $registrasi): TiketKegiatan
    {
        if ($registrasi->status !== 'terdaftar') {
            throw ValidationException::withMessages([
                'registrasi' => 'Tiket hanya dapat diterbitkan untuk registrasi yang berstatus terdaftar.',
            ]);
        }

        return TiketKegiatan::firstOrCreate(
            ['registrasi_kegiatan_id' => $registrasi->id],
            [
                'kode_tiket' => $this->buatKodeTiketUnik(),
                'diterbitkan_pada' => now(),
                'status' => 'aktif',
            ]
        );
    }

    /**
     * Memvalidasi kode tiket dan mencatat presensi secara atomik.
     */
    public function validasiDanCatatPresensi(string $kodeTiket, Kegiatan $kegiatan, User $pengurus): Presensi
    {
        return DB::transaction(function () use ($kodeTiket, $kegiatan, $pengurus) {
            // 1. Cari tiket berdasarkan kode yang sudah di-trim dan kunci barisnya
            $tiket = TiketKegiatan::where('kode_tiket', trim($kodeTiket))
                ->lockForUpdate()
                ->first();

            // 2. Pastikan tiket ditemukan
            if (! $tiket) {
                throw ValidationException::withMessages([
                    'kodeTiket' => 'Kode tiket tidak ditemukan.',
                ]);
            }

            // 3. Ambil relasi registrasi dan pastikan kegiatan_id cocok
            $registrasi = RegistrasiKegiatan::query()
                ->whereKey($tiket->registrasi_kegiatan_id)
                ->lockForUpdate()
                ->first();

            if (! $registrasi || (int) $registrasi->kegiatan_id !== (int) $kegiatan->id) {
                throw ValidationException::withMessages([
                    'kodeTiket' => 'Tiket ini bukan untuk kegiatan yang sedang dipilih.',
                ]);
            }

            if ($registrasi->status !== 'terdaftar') {
                throw ValidationException::withMessages([
                    'kodeTiket' => 'Registrasi peserta ini sudah dibatalkan.',
                ]);
            }

            // 4. Pastikan pemanggil adalah pengurus_ukm dari UKM penyelenggara
            if (! $pengurus->isPengurusUkm() || (int) $pengurus->ukm_id !== (int) $kegiatan->ukm_id) {
                throw ValidationException::withMessages([
                    'kodeTiket' => 'Anda tidak memiliki wewenang untuk mencatat presensi kegiatan UKM ini.',
                ]);
            }

            // 5. Pastikan tiket berstatus aktif (belum dibatalkan)
            if ($tiket->status !== 'aktif') {
                throw ValidationException::withMessages([
                    'kodeTiket' => 'Tiket ini sudah dibatalkan dan tidak berlaku.',
                ]);
            }

            // 6. Pastikan belum ada presensi untuk registrasi_kegiatan_id tersebut
            $sudahHadir = Presensi::where('registrasi_kegiatan_id', $registrasi->id)->exists();

            if ($sudahHadir) {
                throw ValidationException::withMessages([
                    'kodeTiket' => 'Tiket sudah pernah digunakan untuk presensi.',
                ]);
            }

            // 7. Simpan record presensi sesuai kolom tabel presensi
            return Presensi::create([
                'registrasi_kegiatan_id' => $registrasi->id,
                'dicatat_oleh' => $pengurus->id,
                'hadir_pada' => now(),
            ]);
        });
    }
}
