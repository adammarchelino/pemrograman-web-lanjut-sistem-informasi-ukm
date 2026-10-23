<?php

namespace App\Models;

use Database\Factories\PresensiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['registrasi_kegiatan_id', 'dicatat_oleh', 'hadir_pada'])]
class Presensi extends Model
{
    protected $table = 'presensi';

    /** @use HasFactory<PresensiFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['hadir_pada' => 'datetime'];
    }

    public function registrasiKegiatan(): BelongsTo
    {
        return $this->belongsTo(RegistrasiKegiatan::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
