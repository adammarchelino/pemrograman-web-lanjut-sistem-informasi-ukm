<?php

namespace Database\Factories;

use App\Models\Kegiatan;
use App\Models\Ukm;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Kegiatan> */
class KegiatanFactory extends Factory
{
    protected $model = Kegiatan::class;

    public function definition(): array
    {
        $mulai = now()->addWeeks(2)->startOfHour();

        return [
            'ukm_id' => Ukm::factory(),
            'nama' => fake()->sentence(4),
            'deskripsi' => fake()->paragraphs(2, true),
            'mulai_pada' => $mulai,
            'selesai_pada' => $mulai->copy()->addHours(3),
            'lokasi' => fake()->address(),
            'kapasitas' => fake()->numberBetween(20, 150),
            'audiens' => 'mahasiswa_poliwangi',
            'pendaftaran_dibuka_pada' => now(),
            'pendaftaran_ditutup_pada' => $mulai->copy()->subDay(),
            'status' => 'draf',
        ];
    }
}
