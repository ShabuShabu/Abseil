<?php

namespace ShabuShabu\Abseil\Tests;

use Illuminate\Http\Response;
use Orchestra\Testbench\TestCase;
use ShabuShabu\Abseil\Http\Resources\Resource;
use ShabuShabu\Abseil\Tests\App\{Category, Page, User};
use ShabuShabu\Abseil\Tests\Support\AppSetup;

class HttpShowTest extends TestCase
{
    use AppSetup;

    /**
     * @test
     */
    public function ensure_that_a_valid_json_api_resource_is_returned(): void
    {
        $this->actingAs($this->authenticatedUser);

        $page = factory(Page::class)->states('withCategory', 'withUser')->create();

        $response = $this->getJson('pages/' . $page->id, [
            'Accept' => Resource::MEDIA_TYPE,
        ]);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'type',
                         'attributes' => [
                             'title',
                             'content',
                             'createdAt',
                             'updatedAt',
                         ],
                         'links',
                     ],
                 ]);

        $this->assertNull($response->json('included'));
        $this->assertNull($response->json('data.relationships'));
    }

    /**
     * @test
     */
    public function ensure_that_a_valid_json_api_resource_is_returned_including_relationships(): void
    {
        $this->actingAs($this->authenticatedUser);

        $page = factory(Page::class)->states('withCategory', 'withUser')->create();

        $response = $this->getJson('pages/' . $page->id . '?include=user,category', [
            'Accept' => Resource::MEDIA_TYPE,
        ]);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'type',
                         'attributes'    => [
                             'title',
                             'content',
                             'createdAt',
                             'updatedAt',
                         ],
                         'links',
                         'relationships' => [
                             'user'     => [
                                 'data' => [
                                     'id',
                                     'type',
                                 ],
                             ],
                             'category' => [
                                 'data' => [
                                     'id',
                                     'type',
                                 ],
                             ],
                         ],
                     ],
                     'included',
                 ]);

        $ids = collect($response->json('included'))->pluck('id')->toArray();

        $this->assertCount(2, $ids);
        $this->assertContains($page->user->id, $ids);
        $this->assertContains($page->category->id, $ids);

        $this->assertEquals($page->id, $response->json('data.id'));
        $this->assertEquals(Page::jsonType(), $response->json('data.type'));

        $this->assertEquals($page->user->id, $response->json('data.relationships.user.data.id'));
        $this->assertEquals(User::jsonType(), $response->json('data.relationships.user.data.type'));

        $this->assertEquals($page->category->id, $response->json('data.relationships.category.data.id'));
        $this->assertEquals(Category::jsonType(), $response->json('data.relationships.category.data.type'));
    }

    /**
     * @test
     */
    public function ensure_that_a_valid_json_api_resource_is_returned_including_a_collection_relationship(): void
    {
        $this->actingAs($this->authenticatedUser);

        $category = factory(Category::class)->states('withId')->create();

        $pages = factory(Page::class, 3)->create([
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('categories/' . $category->id . '?include=pages', [
            'Accept' => Resource::MEDIA_TYPE,
        ]);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'type',
                         'attributes'    => [
                             'title',
                             'createdAt',
                             'updatedAt',
                         ],
                         'links',
                         'relationships' => [
                             'pages' => [
                                 'data' => [
                                     [
                                         'id',
                                         'type',
                                     ],
                                 ],
                             ],
                         ],
                     ],
                     'included',
                 ]);
    }
}
