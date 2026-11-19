<?php

namespace App\Models;

use Database\Factories\UkmFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_NONAKTIF = 'nonaktif';

    public const OPREC_DIBUKA = 'dibuka';

    public const OPREC_DITUTUP = 'ditutup';

    public function isAktif(): bool
    {
        return $this->status === self::STATUS_AKTIF;
    }

    public function isPendaftaranDibuka(): bool
    {
        return $this->status_pendaftaran_anggota === self::OPREC_DIBUKA;
    }

    public function logoUrl(): ?string
    {
        if (blank($this->logo)) {
            return null;
        }

        if (filter_var($this->logo, FILTER_VALIDATE_URL) || Str::startsWith($this->logo, '/')) {
            return $this->logo;
        }

        return Storage::disk('public')->url($this->logo);
    }

    public function inisial(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->nama));
        if (empty($words) || empty($words[0])) {
            return 'UK';
        }

        // Strip prefix "UKM " if present to get initials of actual name
        if (strtoupper($words[0]) === 'UKM' && isset($words[1])) {
            array_shift($words);
        }

        if (count($words) === 1) {
            return strtoupper(substr($words[0], 0, 2));
        }

        return strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
    }

    public function pengurus(): HasMany
    {
        return $this->hasMany(User::class)->where(function ($query) {
            $query->where('role', User::ROLE_PENGURUS_UKM)->orWhereNotNull('jabatan_ukm');
        });
    }

    public function pengurusAktif(): HasMany
    {
        return $this->pengurus()->where('akun_aktif', true);
    }

    public function divisi(): HasMany
    {
        return $this->hasMany(DivisiUkm::class);
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
