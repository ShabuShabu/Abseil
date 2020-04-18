<?php

namespace ShabuShabu\Abseil\Tests\App\Controllers;

use Illuminate\Http\{Request, Response};
use ShabuShabu\Abseil\Http\Controller;
use ShabuShabu\Abseil\Http\Resources\Collection;
use ShabuShabu\Abseil\Tests\App\Requests\UserRequest;
use ShabuShabu\Abseil\Tests\App\User;

class UserController extends Controller
{
    public function index(Request $request): Collection
    {
        return $this->resourceCollection(User::class, $request);
    }

    public function store(UserRequest $request): Response
    {
        return $this->createResource($request, User::class);
    }

    public function show(Request $request, User $user): UserResponse
    {
        return $this->showResource($request, $user);
    }

    public function update(UserRequest $request, User $user): Response
    {
        return $this->updateResource($request, $user);
    }

    public function destroy(User $user): Response
    {
        return $this->deleteResource($user);
    }

    public function restore(User $user): Response
    {
        return $this->restoreResource($user);
    }
}
