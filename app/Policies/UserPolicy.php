<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function view(User $user)
    {
        return  $user->can('list-user');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-user');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        return $user->can('edit-user');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return $user->can('delete-user');
    }

    /**
     * Determine whether the user can reset password the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function forceResetUserPassword(User $user, User $model)
    {
        return $user->can('force-reset-user-password') && $user->id != $model->id;
    }

    /**
     * Determine whether the user can email the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function email(User $user, User $model)
    {
        return $user->can('email-user') && $user->id != $model->id;
    }

    /**
     * Determine whether the user can perform avatar related action to the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function avatar(User $user, User $model)
    {
        return ($user->id === $model->id) || ($user->id != $model->id && $user->can('edit-user'));
    }
}
