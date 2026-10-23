<?php

namespace Database\Factories;

use App\Models\Keanggotaan;
use App\Models\PendaftaranAnggota;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Keanggotaan> */
class KeanggotaanFactory extends Factory
{
    protected $model = Keanggotaan::class;

    public function definition(): array
    {
        return [
            // Pendaftaran supplies the matching user and UKM; afterMaking fills the corresponding IDs.
            'pendaftaran_anggota_id' => null,
            'tanggal_bergabung' => now()->toDateString(),
            'status' => 'aktif',
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Keanggotaan $keanggotaan) {
            $pendaftaran = $keanggotaan->pendaftaran_anggota_id
                ? PendaftaranAnggota::query()->findOrFail($keanggotaan->pendaftaran_anggota_id)
                : PendaftaranAnggota::factory()->create(['status' => 'diterima']);

            $keanggotaan->pendaftaran_anggota_id = $pendaftaran->id;
            $keanggotaan->user_id = $pendaftaran->user_id;
            $keanggotaan->ukm_id = $pendaftaran->ukm_id;
        });
    }
}
