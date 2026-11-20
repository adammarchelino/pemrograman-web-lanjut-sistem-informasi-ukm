<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'foto_profil', 'password', 'nim', 'role', 'ukm_id', 'akun_aktif', 'jabatan_ukm', 'divisi_ukm_id', 'wajib_ganti_password', 'email_verified_at'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'akun_aktif' => 'boolean',
            'wajib_ganti_password' => 'boolean',
        ];
    }

    public const ROLE_MAHASISWA = 'mahasiswa';

    public const ROLE_PENGURUS_UKM = 'pengurus_ukm';

    public const ROLE_KEMAHASISWAAN = 'kemahasiswaan';

    public function isKemahasiswaan(): bool
    {
        return $this->isActive() && $this->role === self::ROLE_KEMAHASISWAAN;
    }

    public function isPengurusUkm(): bool
    {
        return $this->isActive() && ($this->role === self::ROLE_PENGURUS_UKM
            || ($this->role !== self::ROLE_KEMAHASISWAAN && $this->ukm_id !== null && $this->jabatan_ukm !== null));
    }

    public function isKetuaUkm(?Ukm $ukm = null): bool
    {
        return $this->isPengurusUkm()
            && $this->jabatan_ukm === 'ketua'
            && ($ukm === null || (int) $this->ukm_id === (int) $ukm->id);
    }

    public function isPengurusInti(?Ukm $ukm = null): bool
    {
        return $this->isPengurusUkm()
            && $this->jabatan_ukm === 'inti'
            && ($ukm === null || (int) $this->ukm_id === (int) $ukm->id);
    }

    public function canManageTeam(?Ukm $ukm = null): bool
    {
        return $this->isPengurusInti($ukm) && $this->isActive();
    }

    public function isMahasiswa(): bool
    {
        return $this->isActive() && ($this->role === self::ROLE_MAHASISWA || $this->role === self::ROLE_PENGURUS_UKM);
    }

    public function canManageUkm(?Ukm $ukm = null): bool
    {
        if ($this->isKemahasiswaan()) {
            return true;
        }

        if ($this->isPengurusUkm() && $ukm !== null) {
            return (int) $this->ukm_id === (int) $ukm->id;
        }

        return false;
    }

    public function roleLabel(): string
    {
        if ($this->isKetuaUkm()) {
            return 'Ketua UKM';
        }

        if ($this->isPengurusInti()) {
            return 'Pengurus Inti';
        }

        if ($this->jabatan_ukm === 'divisi') {
            return 'Pengurus Divisi';
        }

        return match ($this->role) {
            self::ROLE_KEMAHASISWAAN => 'Bagian Kemahasiswaan',
            self::ROLE_PENGURUS_UKM => 'Pengurus UKM',
            self::ROLE_MAHASISWA => 'Mahasiswa',
            default => ucfirst(str_replace('_', ' ', (string) $this->role)),
        };
    }

    public function ukm(): BelongsTo
    {
        return $this->belongsTo(Ukm::class);
    }

    public function divisiUkm(): BelongsTo
    {
        return $this->belongsTo(DivisiUkm::class);
    }

    public function isActive(): bool
    {
        return (bool) ($this->akun_aktif ?? true);
    }

    public function pendaftaranAnggota(): HasMany
    {
        return $this->hasMany(PendaftaranAnggota::class);
    }

    public function keputusanPendaftaranAnggota(): HasMany
    {
        return $this->hasMany(PendaftaranAnggota::class, 'diputuskan_oleh');
    }

    public function keanggotaan(): HasMany
    {
        return $this->hasMany(Keanggotaan::class);
    }

    public function registrasiKegiatan(): HasMany
    {
        return $this->hasMany(RegistrasiKegiatan::class);
    }

    public function presensiDicatat(): HasMany
    {
        return $this->hasMany(Presensi::class, 'dicatat_oleh');
    }

    /**
     * URL foto profil; null bila pengguna belum mengunggah foto.
     */
    public function fotoProfilUrl(): ?string
    {
        if (blank($this->foto_profil)) {
            return null;
        }

        return Storage::disk('public')->url($this->foto_profil);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
