<?php

namespace App\Models;

use Database\Factories\UkmFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nama', 'logo', 'deskripsi', 'visi', 'misi', 'email_kontak', 'nomor_kontak', 'status',
    'status_pendaftaran_anggota', 'pendaftaran_anggota_dibuka_pada', 'pendaftaran_anggota_ditutup_pada',
])]
class Ukm extends Model
{
    protected $table = 'ukm';

    /** @use HasFactory<UkmFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'pendaftaran_anggota_dibuka_pada' => 'datetime',
            'pendaftaran_anggota_ditutup_pada' => 'datetime',
        ];
    }

    public function pengurus(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'pengurus_ukm');
    }

    public function pendaftaranAnggota(): HasMany
    {
        return $this->hasMany(PendaftaranAnggota::class);
    }

    public function keanggotaan(): HasMany
    {
        return $this->hasMany(Keanggotaan::class);
    }

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class);
    }
}
