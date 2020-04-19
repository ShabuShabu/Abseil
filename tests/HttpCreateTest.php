<?php

namespace ShabuShabu\Abseil\Tests;

use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use ShabuShabu\Abseil\Http\Resources\Resource;
use ShabuShabu\Abseil\Tests\App\{Category, Page};
use ShabuShabu\Abseil\Tests\Support\AppSetup;

class HttpCreateTest extends TestCase
{
    use AppSetup;

    /**
     * @test
     */
    public function ensure_that_a_json_api_resource_can_be_created_with_an_id(): void
    {
        $this->actingAs($this->authenticatedUser);

        $category = factory(Category::class)->create();
        $pageId   = Str::orderedUuid()->toString();

        $response = $this->postJson('pages', [
            'data' => [
                'id'            => $pageId,
                'type'          => Page::JSON_TYPE,
                'attributes'    => [
                    'title'   => 'About Us',
                    'content' => 'We are cool',
                ],
                'relationships' => [
                    'category' => [
                        'data' => [
                            'type' => Category::JSON_TYPE,
                            'id'   => $category->id,
                        ],
                    ],
                ],
            ],
        ], [
            'Content-Type' => Resource::MEDIA_TYPE,
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertHeader('Location', 'http://localhost/pages/' . $pageId)
                 ->assertHeader('X-Request-ID', $pageId);

        $page = Page::findOrFail($pageId);

        $this->assertSame('About Us', $page->title);
        $this->assertSame('We are cool', $page->content);
        $this->assertSame($category->id, $page->category->id);
    }

    /**
     * @test
     */
    public function ensure_that_a_json_api_resource_can_be_created_without_an_id(): void
    {
        $this->actingAs($this->authenticatedUser);

        $category = factory(Category::class)->create();

        $response = $this->postJson('pages', [
            'data' => [
                'type'          => Page::JSON_TYPE,
                'attributes'    => [
                    'title'   => 'About Us',
                    'content' => 'We are cool',
                ],
                'relationships' => [
                    'category' => [
                        'data' => [
                            'type' => Category::JSON_TYPE,
                            'id'   => $category->id,
                        ],
                    ],
                ],
            ],
        ], [
            'Content-Type' => Resource::MEDIA_TYPE,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $page = Page::findOrFail($response->headers->get('X-Request-ID'));

        $this->assertSame('About Us', $page->title);
        $this->assertSame('We are cool', $page->content);
        $this->assertSame($category->id, $page->category->id);
    }

    /**
     * @test
     */
    public function ensure_that_validation_errors_are_thrown(): void
    {
        $this->actingAs($this->authenticatedUser);

        $response = $this->postJson('pages', [
            'data' => [
                'type'       => Page::JSON_TYPE,
                'attributes' => [
                    'title' => 1,
                ],
            ],
        ], [
            'Content-Type' => Resource::MEDIA_TYPE,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertHeaderMissing('Location')
                 ->assertHeaderMissing('X-Request-ID')
                 ->assertExactJson([
                     'errors' => [
                         [
                             'status' => '422',
                             'detail' => 'The title must be a string',
                             'source' => [
                                 'pointer' => '/data/attributes/title',
                             ],
                         ],
                         [
                             'status' => '422',
                             'detail' => 'The content field is required',
                         ],
                     ],
                 ]);
    }
}
