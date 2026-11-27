<?php

namespace App\Policies;

use App\Models\TiketKegiatan;
use App\Models\User;

class TiketKegiatanPolicy
{
    public function view(User $user, TiketKegiatan $tiket): bool
    {
        return $user->isActive()
            && $user->role === User::ROLE_MAHASISWA
            && ! $user->isPengurusUkm()
            && $tiket->registrasiKegiatan?->user_id === $user->id;
    }
}