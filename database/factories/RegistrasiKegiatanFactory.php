<?php

namespace Database\Factories;

use App\Models\Kegiatan;
use App\Models\RegistrasiKegiatan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RegistrasiKegiatan> */
class RegistrasiKegiatanFactory extends Factory
{
    protected $model = RegistrasiKegiatan::class;

    public function definition(): array
    {
        return [
            'kegiatan_id' => Kegiatan::factory(),
            'user_id' => User::factory(),
            'jenis_peserta' => 'mahasiswa_poliwangi',
            'nama_peserta' => fake()->name(),
            'email_peserta' => fake()->safeEmail(),
            'nomor_kontak' => fake()->phoneNumber(),
            'nim' => fake()->unique()->numerify('##########'),
            'status' => 'terdaftar',
            'terdaftar_pada' => now(),
        ];
    }

    public function pesertaUmum(): static
    {
        return $this->state(fn () => [
            'user_id' => null,
            'jenis_peserta' => 'umum',
            'nim' => null,
        ]);
    }
}
