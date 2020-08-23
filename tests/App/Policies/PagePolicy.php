<?php

namespace ShabuShabu\Abseil\Tests\App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use ShabuShabu\Abseil\Tests\App\{Page, User};

class PagePolicy
{
    use HandlesAuthorization;

    public function overview(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Page $page): bool
    {
        return $user->is_admin || $user->id === $page->user_id;
    }

    public function view(User $user, Page $page): bool
    {
        return true;
    }

    public function delete(User $user, Page $page): bool
    {
        return $user->is_admin || $user->id === $page->user_id;
    }
}
