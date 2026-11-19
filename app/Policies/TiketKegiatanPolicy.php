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
        if (! $user->isActive()) {
            return false;
        }

        $registrasi = $tiket->registrasiKegiatan;
        $kegiatan = $registrasi?->kegiatan;

        if (! $registrasi || ! $kegiatan) {
            return false;
        }

        $isTicketOwner = $user->isMahasiswa()
            && (int) $registrasi->user_id === (int) $user->id;
        $isAssignedOrganizer = $user->isPengurusUkm()
            && (int) $kegiatan->ukm_id === (int) $user->ukm_id;

        return $isTicketOwner || $isAssignedOrganizer;
    }
}
