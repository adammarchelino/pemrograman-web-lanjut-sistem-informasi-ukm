<?php

namespace Database\Factories;

use App\Models\PendaftaranAnggota;
use App\Models\Ukm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PendaftaranAnggota> */
class PendaftaranAnggotaFactory extends Factory
{
    protected $model = PendaftaranAnggota::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'ukm_id' => Ukm::factory(),
            'jawaban_formulir' => ['alasan' => fake()->sentence(), 'pengalaman' => fake()->paragraph()],
            'status' => 'pending',
            'diajukan_pada' => now(),
            'catatan_pengurus' => null,
            'diputuskan_oleh' => null,
            'diputuskan_pada' => null,
        ];
    }
}
