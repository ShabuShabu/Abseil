<?php

namespace ShabuShabu\Abseil\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ShabuShabu\Abseil\AbseilServiceProvider;
use function ShabuShabu\Abseil\{inflate, resource_guard, to_camel_case, tokenize, tokenize_all};

class HelpersTest extends TestCase
{
    /**
     * @test
     */
    public function ensure_that_array_keys_can_be_transformed_to_camel_case(): void
    {
        $input = [
            'key_one' => 'test',
            'keyTwo'  => [
                'key_three' => 'foo',
                'key_four'  => 'bar',
            ],
        ];

        $expected = [
            'keyOne' => 'test',
            'keyTwo' => [
                'keyThree' => 'foo',
                'keyFour'  => 'bar',
            ],
        ];

        $this->assertSame($expected, to_camel_case($input));
    }

    /**
     * @test
     */
    public function ensure_that_the_resource_guard_throws_an_exception_for_a_non_existing_class(): void
    {
        $this->expectException(InvalidArgumentException::class);

        resource_guard('ShabuShabu\\Abseil\\Fake\\Class');
    }

    /**
     * @test
     */
    public function ensure_that_the_resource_guard_does_not_throw_an_exception_for_an_existing_class(): void
    {
        try {
            resource_guard(AbseilServiceProvider::class);
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(false);

            return;
        }

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function ensure_that_a_dotted_array_gets_inflated_properly(): void
    {
        $array = [
            'some.nested.value' => true,
            'some.other'        => false,
            'entirely'          => 'yup',
        ];

        $actual   = inflate($array);
        $expected = [
            'some'     => [
                'nested' => [
                    'value' => true,
                ],
                'other'  => false,
            ],
            'entirely' => 'yup',
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function ensure_that_a_dotted_string_can_be_tokenized(): void
    {
        $string = 'user.roles.just.a.test';

        $this->assertEquals('user', tokenize($string, 1));
        $this->assertEquals('user.roles', tokenize($string, 2));
        $this->assertEquals('user.roles.just', tokenize($string, 3));
        $this->assertEquals('user.roles.just.a', tokenize($string, 4));
        $this->assertEquals('user.roles.just.a.test', tokenize($string, 5));
        $this->assertEquals('user.roles.just.a.test', tokenize($string, 27));
    }

    /**
     * @test
     */
    public function ensure_that_a_dotted_string_can_be_broken_into_tokens(): void
    {
        $expected = [
            'user',
            'user.roles',
            'user.roles.just',
            'user.roles.just.a',
            'user.roles.just.a.test',
        ];

        $this->assertEquals($expected, tokenize_all('user.roles.just.a.test'));
    }
}
