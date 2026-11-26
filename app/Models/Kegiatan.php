<?php

namespace App\Models;

use Database\Factories\KegiatanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'ukm_id', 'nama', 'deskripsi', 'mulai_pada', 'selesai_pada', 'lokasi', 'kapasitas', 'audiens',
    'pendaftaran_dibuka_pada', 'pendaftaran_ditutup_pada', 'status',
])]
class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    /** @use HasFactory<KegiatanFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'mulai_pada' => 'datetime',
            'selesai_pada' => 'datetime',
            'pendaftaran_dibuka_pada' => 'datetime',
            'pendaftaran_ditutup_pada' => 'datetime',
            'kapasitas' => 'integer',
        ];
    }

    public function ukm(): BelongsTo
    {
        return $this->belongsTo(Ukm::class);
    }

    public function registrasiKegiatan(): HasMany
    {
        return $this->hasMany(RegistrasiKegiatan::class);
    }
}
