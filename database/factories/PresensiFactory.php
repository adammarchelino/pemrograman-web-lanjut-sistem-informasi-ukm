<?php

namespace Database\Factories;

use App\Models\Presensi;
use App\Models\RegistrasiKegiatan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Presensi> */
class PresensiFactory extends Factory
{
    protected $model = Presensi::class;

    public function definition(): array
    {
        return [
            'registrasi_kegiatan_id' => RegistrasiKegiatan::factory(),
            'dicatat_oleh' => User::factory(),
            'hadir_pada' => now(),
        ];
    }
}
