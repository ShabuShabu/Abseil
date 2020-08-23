<?php

namespace ShabuShabu\Abseil\Tests;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\TestCase;
use ShabuShabu\Abseil\Events\ResourceRestored;
use ShabuShabu\Abseil\Tests\App\{User};
use ShabuShabu\Abseil\Tests\Support\AppSetup;

class HttpRestoreTest extends TestCase
{
    use AppSetup;

    /**
     * @test
     */
    public function ensure_that_a_trashed_json_api_resource_can_be_restored(): void
    {
        Event::fake([ResourceRestored::class]);

        $this->actingAs(
            factory(User::class)->states('admin')->create()
        );

        $user = factory(User::class)->states('trashed')->create();

        $this->putJson('users/' . $user->id . '/restore?filter[trashed]=with')
             ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'deleted_at' => null,
        ]);

        Event::assertDispatched(ResourceRestored::class);
    }

    /**
     * @test
     */
    public function ensure_that_a_non_trashed_json_api_resource_cannot_be_restored(): void
    {
        Event::fake([ResourceRestored::class]);

        $this->actingAs(
            factory(User::class)->states('admin')->create()
        );

        $user = factory(User::class)->create();

        $this->putJson('users/' . $user->id . '/restore')
             ->assertStatus(Response::HTTP_NOT_MODIFIED);

        Event::assertNotDispatched(ResourceRestored::class);
    }
}
