<?php

namespace ShabuShabu\Abseil\Tests\App\Controllers;

use Illuminate\Http\{Request, Response};
use ShabuShabu\Abseil\Http\Controller;
use ShabuShabu\Abseil\Http\Resources\Collection;
use ShabuShabu\Abseil\Tests\App\Category;
use ShabuShabu\Abseil\Tests\App\Requests\CategoryRequest;
use ShabuShabu\Abseil\Tests\App\Resources\Category as CategoryResource;

class CategoryController extends Controller
{
    public function index(Request $request): Collection
    {
        return $this->resourceCollection(Category::class, $request);
    }

    public function store(CategoryRequest $request): Response
    {
        return $this->createResource($request, Category::class);
    }

    public function show(Request $request, Category $category): CategoryResource
    {
        return $this->showResource($request, $category);
    }

    public function update(CategoryRequest $request, Category $category): Response
    {
        return $this->updateResource($request, $category);
    }

    public function destroy(Category $category): Response
    {
        return $this->deleteResource($category);
    }
}
