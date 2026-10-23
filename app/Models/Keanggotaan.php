<?php

namespace App\Models;

use Database\Factories\KeanggotaanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'ukm_id', 'pendaftaran_anggota_id', 'tanggal_bergabung', 'status'])]
class Keanggotaan extends Model
{
    protected $table = 'keanggotaan';

    /** @use HasFactory<KeanggotaanFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['tanggal_bergabung' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ukm(): BelongsTo
    {
        return $this->belongsTo(Ukm::class);
    }

    public function pendaftaranAnggota(): BelongsTo
    {
        return $this->belongsTo(PendaftaranAnggota::class);
    }
}
