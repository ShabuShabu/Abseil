<?php

namespace ShabuShabu\Abseil\Tests;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\TestCase;
use ShabuShabu\Abseil\Events\{ResourceRelationshipSaved, ResourceUpdated};
use ShabuShabu\Abseil\Http\Resources\Resource;
use ShabuShabu\Abseil\Tests\App\{Category, Page};
use ShabuShabu\Abseil\Tests\Support\AppSetup;

class HttpPatchTest extends TestCase
{
    use AppSetup;

    /**
     * @test
     */
    public function ensure_that_a_json_api_resource_can_be_updated(): void
    {
        Event::fake([ResourceUpdated::class, ResourceRelationshipSaved::class]);

        $this->actingAs($this->authenticatedUser);

        $page = factory(Page::class)->create([
            'title'   => 'About',
            'content' => 'We are cool',
        ]);

        $response = $this->patchJson('pages/' . $page->id, [
            'data' => [
                'id'         => $page->id,
                'type'       => Page::jsonType(),
                'attributes' => [
                    'title' => 'About Us',
                ],
            ],
        ], [
            'Content-Type' => Resource::MEDIA_TYPE,
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $page = $page->refresh();

        $this->assertSame('About Us', $page->title);
        $this->assertSame('We are cool', $page->content);

        Event::assertDispatched(ResourceUpdated::class);
        Event::assertNotDispatched(ResourceRelationshipSaved::class);
    }

    /**
     * @test
     */
    public function ensure_that_a_json_api_resource_with_relationships_can_be_updated(): void
    {
        Event::fake([ResourceUpdated::class, ResourceRelationshipSaved::class]);

        $this->actingAs($this->authenticatedUser);

        $page = factory(Page::class)->states('withCategory')->create([
            'title'   => 'About',
            'content' => 'We are cool',
        ]);

        $category = factory(Category::class)->create();

        $response = $this->patchJson('pages/' . $page->id, [
            'data' => [
                'id'            => $page->id,
                'type'          => Page::jsonType(),
                'attributes'    => [
                    'title' => 'About Us',
                ],
                'relationships' => [
                    'category' => [
                        'data' => [
                            'type' => Category::jsonType(),
                            'id'   => $category->id,
                        ],
                    ],
                ],
            ],
        ], [
            'Content-Type' => Resource::MEDIA_TYPE,
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $page = $page->refresh();

        $this->assertSame('About Us', $page->title);
        $this->assertSame('We are cool', $page->content);
        $this->assertSame($category->id, $page->category->id);

        Event::assertDispatched(ResourceUpdated::class);
        Event::assertDispatched(ResourceRelationshipSaved::class);
    }

    /**
     * @test
     */
    public function ensure_that_validation_errors_are_thrown_for_an_invalid_patch_request(): void
    {
        Event::fake([ResourceUpdated::class, ResourceRelationshipSaved::class]);

        $this->actingAs($this->authenticatedUser);

        $page = factory(Page::class)->states('withCategory')->create([
            'title'   => 'About',
            'content' => 'We are cool',
        ]);

        $response = $this->patchJson('pages/' . $page->id, [
            'data' => [
                'id'         => $page->id,
                'type'       => Page::jsonType(),
                'attributes' => [
                    'title' => 1,
                ],
            ],
        ], [
            'Content-Type' => Resource::MEDIA_TYPE,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertExactJson([
                     'errors' => [
                         [
                             'status' => '422',
                             'detail' => 'The title must be a string',
                             'source' => [
                                 'pointer' => '/data/attributes/title',
                             ],
                         ],
                     ],
                 ]);

        Event::assertNotDispatched(ResourceUpdated::class);
        Event::assertNotDispatched(ResourceRelationshipSaved::class);
    }
}
