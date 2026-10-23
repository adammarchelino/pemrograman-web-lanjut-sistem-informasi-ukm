<?php

namespace Database\Factories;

use App\Models\RegistrasiKegiatan;
use App\Models\TiketKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<TiketKegiatan> */
class TiketKegiatanFactory extends Factory
{
    protected $model = TiketKegiatan::class;

    public function definition(): array
    {
        return [
            'registrasi_kegiatan_id' => RegistrasiKegiatan::factory(),
            'kode_tiket' => Str::random(48),
            'diterbitkan_pada' => now(),
            'status' => 'aktif',
        ];
    }
}
