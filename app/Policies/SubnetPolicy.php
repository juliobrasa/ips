<?php

namespace App\Policies;

use App\Models\Subnet;
use App\Models\User;

class SubnetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->company && $user->company->isHolder();
    }

    public function view(User $user, Subnet $subnet): bool
    {
        // Owner can view
        if ($user->company && $subnet->company_id === $user->company->id) {
            return true;
        }

        // Admin can view
        if ($user->role === 'admin') {
            return true;
        }

        // Lessee with active lease can view
        if ($user->company) {
            return $subnet->leases()
                ->where('lessee_company_id', $user->company->id)
                ->where('status', 'active')
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->company && $user->company->canList();
    }

    public function update(User $user, Subnet $subnet): bool
    {
        // Owner can update
        if ($user->company && $user->company->id === $subnet->company_id) {
            return true;
        }

        // Admin can update
        return $user->role === 'admin';
    }

    public function delete(User $user, Subnet $subnet): bool
    {
        // Owner can delete if not leased
        if ($user->company && $user->company->id === $subnet->company_id) {
            return !$subnet->isLeased();
        }

        // Admin can delete if not leased
        if ($user->role === 'admin') {
            return !$subnet->isLeased();
        }

        return false;
    }
}
