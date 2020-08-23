<?php

namespace ShabuShabu\Abseil\Tests\App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use ShabuShabu\Abseil\Tests\App\{Category, User};

class CategoryPolicy
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

    public function update(User $user, Category $category): bool
    {
        return true;
    }

    public function view(User $user, Category $category): bool
    {
        return true;
    }

    public function delete(User $user, Category $category): bool
    {
        return true;
    }
}
