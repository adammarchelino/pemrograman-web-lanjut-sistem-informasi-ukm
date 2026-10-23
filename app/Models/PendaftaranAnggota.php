<?php

namespace App\Models;

use Database\Factories\PendaftaranAnggotaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

#[Fillable([
    'user_id', 'ukm_id', 'jawaban_formulir', 'status', 'diajukan_pada', 'catatan_pengurus',
    'diputuskan_oleh', 'diputuskan_pada',
])]
class PendaftaranAnggota extends Model
{
    protected $table = 'pendaftaran_anggota';

    /** @use HasFactory<PendaftaranAnggotaFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'jawaban_formulir' => 'array',
            'diajukan_pada' => 'datetime',
            'diputuskan_pada' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ukm(): BelongsTo
    {
        return $this->belongsTo(Ukm::class);
    }

    public function pemutus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diputuskan_oleh');
    }

    public function keanggotaan(): HasOne
    {
        return $this->hasOne(Keanggotaan::class);
    }

    /** Accept this pending application and create or reactivate its single membership atomically. */
    public function terima(User $pemutus, ?Carbon $tanggalBergabung = null): Keanggotaan
    {
        return DB::transaction(function () use ($pemutus, $tanggalBergabung) {
            $pendaftaran = static::query()->lockForUpdate()->findOrFail($this->getKey());

            if ($pendaftaran->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Hanya pengajuan pending yang dapat diterima.']);
            }

            $keanggotaan = Keanggotaan::query()->firstOrNew([
                'user_id' => $pendaftaran->user_id,
                'ukm_id' => $pendaftaran->ukm_id,
            ]);
            $keanggotaan->pendaftaran_anggota_id = $pendaftaran->id;
            $keanggotaan->tanggal_bergabung = $tanggalBergabung ?? now()->toDateString();
            $keanggotaan->status = 'aktif';
            $keanggotaan->save();

            $pendaftaran->forceFill([
                'status' => 'diterima',
                'diputuskan_oleh' => $pemutus->getKey(),
                'diputuskan_pada' => now(),
            ])->save();

            return $keanggotaan;
        });
    }
}
