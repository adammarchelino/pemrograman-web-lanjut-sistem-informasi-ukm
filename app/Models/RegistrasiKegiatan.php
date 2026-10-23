<?php

namespace App\Models;

use Database\Factories\RegistrasiKegiatanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'kegiatan_id', 'user_id', 'jenis_peserta', 'nama_peserta', 'email_peserta', 'nomor_kontak', 'nim',
    'status', 'terdaftar_pada',
])]
class RegistrasiKegiatan extends Model
{
    protected $table = 'registrasi_kegiatan';

    /** @use HasFactory<RegistrasiKegiatanFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['terdaftar_pada' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $registrasi) {
            $registrasi->email_peserta = mb_strtolower(trim($registrasi->email_peserta));
        });
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tiketKegiatan(): HasOne
    {
        return $this->hasOne(TiketKegiatan::class);
    }

    public function presensi(): HasOne
    {
        return $this->hasOne(Presensi::class);
    }
}
