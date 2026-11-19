<?php

namespace App\Policies;

use App\Models\Ukm;
use App\Models\User;

class UkmPolicy
{
    public function viewCatalog(User $user): bool
    {
        return $user->isMahasiswa();
    }

    /**
     * Determine whether the user can view any UKM.
     */
    public function viewAny(User $user): bool
    {
        return $user->isKemahasiswaan();
    }

    /**
     * Determine whether the user can view the UKM.
     */
    public function view(User $user, Ukm $ukm): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can create UKM.
     */
    public function create(User $user): bool
    {
        return $user->isKemahasiswaan();
    }

    /**
     * Determine whether the user can update the UKM.
     * Kemahasiswaan can update any UKM; only the assigned Ketua may edit the public profile.
     */
    public function update(User $user, Ukm $ukm): bool
    {
        if ($user->isKemahasiswaan()) {
            return true;
        }

        return $user->isKetuaUkm($ukm);
    }

    /**
     * Determine whether the user can toggle status (activate/deactivate) of the UKM.
     */
    public function toggleStatus(User $user, Ukm $ukm): bool
    {
        return $user->isKemahasiswaan();
    }

    /**
     * Determine whether the user can delete the UKM.
     */
    public function delete(User $user, Ukm $ukm): bool
    {
        return $user->isKemahasiswaan();
    }
}
