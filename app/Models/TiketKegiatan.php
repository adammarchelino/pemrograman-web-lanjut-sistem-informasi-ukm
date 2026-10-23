<?php

namespace App\Models;

use Database\Factories\TiketKegiatanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['registrasi_kegiatan_id', 'kode_tiket', 'diterbitkan_pada', 'status'])]
class TiketKegiatan extends Model
{
    protected $table = 'tiket_kegiatan';

    /** @use HasFactory<TiketKegiatanFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['diterbitkan_pada' => 'datetime'];
    }

    public function registrasiKegiatan(): BelongsTo
    {
        return $this->belongsTo(RegistrasiKegiatan::class);
    }
}
