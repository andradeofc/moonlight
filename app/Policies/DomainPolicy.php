<?php

namespace App\Policies;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DomainPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Domain  $domain
     * @return bool
     */
    public function view(User $user, Domain $domain)
    {
        return $user->id === $domain->user_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Domain  $domain
     * @return bool
     */
    public function update(User $user, Domain $domain)
    {
        return $user->id === $domain->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Domain  $domain
     * @return bool
     */
    public function delete(User $user, Domain $domain)
    {
        return $user->id === $domain->user_id;
    }

    /**
     * Determine whether the user can verify the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Domain  $domain
     * @return bool
     */
    public function verify(User $user, Domain $domain)
    {
        return $user->id === $domain->user_id;
    }
}