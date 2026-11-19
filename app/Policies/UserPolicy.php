<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models (user management).
     */
    public function viewAny(User $user): bool
    {
        return $user->isKemahasiswaan();
    }

    public function manageTeam(User $user): bool
    {
        return $user->canManageTeam();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isKemahasiswaan() || (int) $user->id === (int) $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isKemahasiswaan();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isKemahasiswaan() || (int) $user->id === (int) $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Kemahasiswaan can delete, but cannot delete themselves
        return $user->isKemahasiswaan() && (int) $user->id !== (int) $model->id;
    }
}
