<?php

namespace Database\Factories;

use App\Models\Ukm;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Ukm> */
class UkmFactory extends Factory
{
    protected $model = Ukm::class;

    public function definition(): array
    {
        $nama = fake()->unique()->words(2, true);

        return [
            'nama' => ucfirst($nama),
            'logo' => null,
            'deskripsi' => fake()->paragraph(),
            'visi' => fake()->sentence(),
            'misi' => fake()->paragraph(),
            'email_kontak' => fake()->safeEmail(),
            'nomor_kontak' => fake()->phoneNumber(),
            'status' => 'aktif',
            'status_pendaftaran_anggota' => 'dibuka',
            'pendaftaran_anggota_dibuka_pada' => now()->subDay(),
            'pendaftaran_anggota_ditutup_pada' => now()->addMonth(),
        ];
    }
}
