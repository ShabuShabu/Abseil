<?php

namespace ShabuShabu\Abseil\Tests\App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use ShabuShabu\Abseil\Tests\App\User;

class UserPolicy
{
    use HandlesAuthorization;

    public function overview(User $user): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $loggedInUser, User $user): bool
    {
        return $loggedInUser->is_admin || $loggedInUser->id === $user->id;
    }

    public function view(User $loggedInUser, User $user): bool
    {
        return $loggedInUser->is_admin || $loggedInUser->id === $user->id;
    }

    public function delete(User $loggedInUser, User $user): bool
    {
        return $loggedInUser->is_admin || $loggedInUser->id === $user->id;
    }
}
