<?php

namespace ShabuShabu\Abseil\Tests;

use Illuminate\Http\Response;
use Orchestra\Testbench\TestCase;
use ShabuShabu\Abseil\Tests\App\Page;
use ShabuShabu\Abseil\Tests\Support\AppSetup;

class HttpTest extends TestCase
{
    use AppSetup;

    /**
     * @test
     * @group fail
     */
    public function ensure_that_a_valid_json_api_collection_is_returned(): void
    {
        $this->actingAs($this->authenticatedUser);

        factory(Page::class, 2)->states('withCategory', 'withUser')->create();

        $response = $this->getJson('pages');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([
                     'data'  => [
                         [
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
                     ],
                     'links' => [
                         'first',
                         'last',
                         'prev',
                         'next',
                     ],
                     'meta'  => [
                         'pagination' => [
                             'currentPage',
                             'lastPage',
                             'from',
                             'path',
                             'perPage',
                             'to',
                             'total',
                         ],
                     ],
                 ]);
    }
}
