<?php

namespace ShabuShabu\Abseil\Tests\Support\Controllers;

use Illuminate\Http\{Request, Response};
use ShabuShabu\Abseil\Http\{Collection, Controller};
use ShabuShabu\Abseil\Tests\Support\Requests\UserRequest;
use ShabuShabu\Abseil\Tests\Support\User;

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
