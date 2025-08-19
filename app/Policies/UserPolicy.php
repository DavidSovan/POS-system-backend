<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view their own profile, admins can view any user
        return $user->id === $model->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile (limited fields)
        // Admins can update any user
        return $user->id === $model->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Only admins can delete users, and they cannot delete themselves
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can manage roles and status.
     */
    public function manageRoleAndStatus(User $user, User $model): bool
    {
        // Only admins can change roles and status, and they cannot change their own role
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can access sales APIs.
     */
    public function accessSales(User $user): bool
    {
        return $user->isAdmin() || $user->isManager() || $user->isCashier();
    }

    /**
     * Determine whether the user can manage sales (create, update, delete).
     */
    public function manageSales(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine whether the user can access inventory APIs.
     */
    public function accessInventory(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine whether the user can access reports.
     */
    public function accessReports(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine whether the user can access system configuration.
     */
    public function accessSystemConfig(User $user): bool
    {
        return $user->isAdmin();
    }
}