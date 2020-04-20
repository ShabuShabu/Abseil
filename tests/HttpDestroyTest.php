<?php

namespace ShabuShabu\Abseil\Tests;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\TestCase;
use ShabuShabu\Abseil\Events\ResourceDeleted;
use ShabuShabu\Abseil\Tests\App\{Page, User};
use ShabuShabu\Abseil\Tests\Support\AppSetup;

class HttpDestroyTest extends TestCase
{
    use AppSetup;

    /**
     * @test
     */
    public function ensure_that_a_json_api_resource_can_be_deleted(): void
    {
        Event::fake([ResourceDeleted::class]);

        $this->actingAs($this->authenticatedUser);

        $page = factory(Page::class)->states('withCategory')->create();

        $response = $this->deleteJson('pages/' . $page->id);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('pages', [
            'id' => $page->id,
        ]);

        Event::assertDispatched(ResourceDeleted::class);
    }

    /**
     * @test
     */
    public function ensure_that_a_soft_delete_enabled_json_api_resource_can_be_trashed_and_deleted(): void
    {
        Event::fake([ResourceDeleted::class]);

        $this->actingAs(
            factory(User::class)->states('admin')->create()
        );

        $user = factory(User::class)->create();

        $this->deleteJson('users/' . $user->id)
             ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        $user = $user->refresh();

        $this->assertTrue($user->trashed());

        $this->deleteJson('users/' . $user->id . '?filter[trashed]=with')
             ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        Event::assertDispatched(ResourceDeleted::class, 2);
    }
}
