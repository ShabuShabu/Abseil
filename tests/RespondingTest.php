<?php

namespace ShabuShabu\Abseil\Tests;

use Orchestra\Testbench\TestCase;

class RespondingTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
