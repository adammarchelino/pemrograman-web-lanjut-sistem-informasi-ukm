<?php

namespace App\Policies;

use App\Models\TiketKegiatan;
use App\Models\User;

class TiketKegiatanPolicy
{
    /**
     * Menentukan apakah pengguna berhak melihat detail e-tiket tertentu.
     */
    public function view(User $user, TiketKegiatan $tiket): bool
    {
        $registrasi = $tiket->registrasiKegiatan;
        $kegiatan = $registrasi?->kegiatan;

        if (! $registrasi || ! $kegiatan) {
            return false;
        }

        if ($user->role === 'mahasiswa') {
            return $registrasi->user_id === $user->id;
        }

        if ($user->role === 'pengurus_ukm') {
            return $kegiatan->ukm_id === $user->ukm_id;
        }

        return false;
    }
}