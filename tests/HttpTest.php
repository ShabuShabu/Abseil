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
    public function ensure_true_is_true(): void
    {
        $this->actingAs($this->authenticatedUser);

        factory(Page::class, 2)->states('withCategory', 'withUser')->create();

        $response = $this->getJson('pages');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure(
                     $this->collectionStructure([
                         'title',
                         'content',
                         'createdAt',
                         'updatedAt',
                     ])
                 );
    }
}
