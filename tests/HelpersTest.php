<?php

namespace ShabuShabu\Abseil\Tests;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
