<?php

namespace ShabuShabu\Abseil\Tests\App\Controllers;

use Illuminate\Http\{Request, Response};
use ShabuShabu\Abseil\Http\Controller;
use ShabuShabu\Abseil\Http\Resources\Collection;
use ShabuShabu\Abseil\Tests\App\Page;
use ShabuShabu\Abseil\Tests\App\Requests\PageRequest;

class PageController extends Controller
{
    public function index(Request $request): Collection
    {
        return $this->resourceCollection(Page::class, $request);
    }

    public function store(PageRequest $request): Response
    {
        return $this->createResource($request, Page::class);
    }

    public function show(Request $request, Page $page): PageResponse
    {
        return $this->showResource($request, $page);
    }

    public function update(PageRequest $request, Page $page): Response
    {
        return $this->updateResource($request, $page);
    }

    public function destroy(Page $page): Response
    {
        return $this->deleteResource($page);
    }
}
